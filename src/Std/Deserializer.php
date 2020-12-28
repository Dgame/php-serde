<?php

namespace Dgame\Serde\Std;

use Dgame\Serde\Annotation\PropertyAnnotation;
use Dgame\Serde\Meta;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use RuntimeException;
use stdClass;

final class Deserializer
{
    /**
     * @param string|object $class
     * @param mixed         $input
     *
     * @return object|null
     * @throws ReflectionException
     */
    public function deserialize(string|object $class, stdClass $input): ?object
    {
        $refl = new ReflectionClass($class);

        return $this->hydrate(is_object($class) ? $class : null, $refl, $input);
    }

    /**
     * @param object|null     $object
     * @param ReflectionClass $reflection
     * @param stdClass        $input
     *
     * @return object|null
     * @throws ReflectionException
     */
    private function hydrate(?object $object, ReflectionClass $reflection, stdClass $input): ?object
    {
        $object ??= $this->createObject($reflection);
        foreach ($reflection->getProperties() as $property) {
            $meta = $this->collectMetaDataFrom($property);
            if ($meta->isIgnoredByDeserialize()) {
                continue;
            }

            $name = $this->findPropertyNameIn($meta, $input);
            if ($name === null) {
                $this->applyDefaultValue($meta, $object, $property);

                continue;
            }

            $value = $input->{$name};
            if (($reflection = $meta->getClassReflection()) !== null) {
                $value = match (true) {
                    $this->isSingleObject($value) => $this->hydrate(null, $reflection, $value),
                    $this->isArrayOfObjects($value) => $this->hydrateAll($value, $reflection),
                    default => $value
                };
            }

            if (($closure = $meta->getDeserializeWith()) !== null) {
                $value = $closure($value);
            }

            $this->setValue($meta, $object, $value, $property);
        }

        return $object;
    }

    /**
     * @param ReflectionClass $reflection
     *
     * @return object
     * @throws ReflectionException
     */
    private function createObject(ReflectionClass $reflection): object
    {
        $ctor = $reflection->getConstructor();
        if ($ctor !== null && $ctor->getNumberOfRequiredParameters() !== 0) {
            return $reflection->newInstanceWithoutConstructor();
        }

        return $reflection->newInstance();
    }

    private function collectMetaDataFrom(ReflectionProperty $property): Meta
    {
        $meta = new Meta($property);
        foreach ($property->getAttributes() as $attribute) {
            $annotation = $attribute->newInstance();
            if ($annotation instanceof PropertyAnnotation) {
                $annotation->apply($meta);
            }
        }

        return $meta;
    }

    private function findPropertyNameIn(Meta $meta, stdClass $object): ?string
    {
        $alias = [
            $meta->getName(),
            $meta->getSerializeAs(),
            $meta->getDeserializeAs(),
            ...$meta->getAlias()
        ];
        foreach ($alias as $name) {
            if (property_exists($object, $name)) {
                return $name;
            }
        }

        return null;
    }

    private function applyDefaultValue(Meta $meta, object $object, ReflectionProperty $property): void
    {
        if ($meta->isOptional()) {
            $this->setValue($meta, $object, $meta->getDefaultValue(), $property);

            return;
        }

        throw new RuntimeException('Not optional value for ' . $property->getName());
    }

    #[Pure]
    private function isSingleObject(mixed $value): bool
    {
        return $value instanceof stdClass;
    }

    #[Pure]
    private function isArrayOfObjects(mixed $value): bool
    {
        if (!is_array($value)) {
            return false;
        }

        $first = current($value);

        return $first instanceof stdClass;
    }

    /**
     * @param object[]        $objects
     * @param ReflectionClass $reflection
     *
     * @return array
     * @throws ReflectionException
     */
    private function hydrateAll(array $objects, ReflectionClass $reflection): array
    {
        $output = [];
        foreach ($objects as $key => $object) {
            $output[$key] = $this->hydrate(null, $reflection, $object);
        }

        return $output;
    }

    private function setValue(Meta $meta, object $object, mixed $value, ReflectionProperty $property): void
    {
        if (($method = $meta->getSetter()) !== null) {
            $object->{$method}($value);
        } else {
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }
    }
}

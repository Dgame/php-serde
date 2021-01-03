<?php

namespace Dgame\Serde\Deserializer;

use Dgame\Serde\Annotation\Alias;
use Dgame\Serde\Annotation\ArrayOf;
use Dgame\Serde\Annotation\DefaultValue;
use Dgame\Serde\Annotation\Rename;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;
use stdClass;

final class UserDefinedObjectDeserializer implements Deserializer
{
    /**
     * @var array<string, Deserializer>
     */
    private array $propertyDeserializer = [];
    /**
     * @var array<string, string[]>
     */
    private array $alias = [];

    public function __construct(private ReflectionClass $reflection)
    {
        foreach ($reflection->getProperties() as $property) {
            $propertyDeserializer = new MixedValueDeserializer();

            /** @var ReflectionNamedType|null $type */
            $type = $property->getType();
            if ($type !== null) {
                $propertyDeserializer = $this->makeTypeDeserializer($property, $type) ?? $propertyDeserializer;
            }

            $propertyName = $property->getName();
            foreach ($property->getAttributes(Alias::class) as $attribute) {
                /** @var Alias $annotation */
                $annotation = $attribute->newInstance();

                $this->setAlias($annotation->alias, $propertyName);
            }

            foreach ($property->getAttributes(Rename::class) as $attribute) {
                /** @var Rename $annotation */
                $annotation = $attribute->newInstance();
                if (!empty($annotation->deserialize)) {
                    $this->setAlias($annotation->deserialize, $propertyName);
                }
            }

            foreach ($property->getAttributes(DefaultValue::class) as $attribute) {
                /** @var DefaultValue $annotation */
                $annotation           = $attribute->newInstance();
                $propertyDeserializer = new DefaultValueDeserializer($propertyDeserializer, $annotation->value);
            }

            $this->setDeserializer($propertyName, $propertyDeserializer);
        }
    }

    private function makeTypeDeserializer(ReflectionProperty $property, ReflectionNamedType $type): ?Deserializer
    {
        if ($type->isBuiltin() && $type->getName() === 'array') {
            $propertyDeserializer = null;
            foreach ($property->getAttributes(ArrayOf::class) as $attribute) {
                /** @var ArrayOf $default */
                $annotation = $attribute->newInstance();

                $propertyDeserializer = new ArrayDeserializer(DeserializerReflectionTypeFactory::parse($annotation->type));
            }

            return $propertyDeserializer;
        }

        return DeserializerReflectionTypeFactory::fromReflectionNamedType($type);
    }

    public function getDefaultValue(): ?object
    {
        return null;
    }

    public function setDeserializer(string $propertyName, Deserializer $deserializer): void
    {
        $this->propertyDeserializer[$propertyName] = $deserializer;
    }

    public function setAlias(string $alias, string $propertyName): void
    {
        $this->alias[$propertyName][] = $alias;
    }

    public function deserialize(mixed $input): object
    {
        assert($input instanceof stdClass);

        $object = $this->reflection->newInstanceWithoutConstructor();
        foreach ($this->propertyDeserializer as $propertyName => $deserializer) {
            if (!$this->reflection->hasProperty($propertyName)) {
                continue;
            }

            $property = $this->reflection->getProperty($propertyName);
            $value = $this->extractValue($input, $propertyName);
            if ($value === null && $property->hasDefaultValue()) {
                continue;
            }

            $value = $deserializer->deserialize($value);
            if ($value !== null || $this->isNullValidArgument($property)) {
                $property->setAccessible(true);
                $property->setValue($object, $value);
            }
        }

        return $object;
    }

    private function isNullValidArgument(ReflectionProperty $property): bool
    {
        $type = $property->getType();
        if ($type === null) {
            return true;
        }

        return $type->allowsNull();
    }

    private function extractValue(stdClass $input, string $name): mixed
    {
        $names = [$name, ...$this->alias[$name] ?? []];
        foreach ($names as $alias) {
            if (!property_exists($input, $alias)) {
                continue;
            }

            return $input->{$alias};
        }

        return null;
    }
}

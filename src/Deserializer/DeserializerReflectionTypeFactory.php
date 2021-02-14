<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

use ReflectionClass;
use ReflectionException;
use ReflectionNamedType;
use ReflectionType;
use ReflectionUnionType;

final class DeserializerReflectionTypeFactory
{
    public static function parse(string $type): Deserializer
    {
        return match ($type) {
            'string' => new StringDeserializer(),
            'int' => new IntDeserializer(),
            'float' => new FloatDeserializer(),
            'bool' => new BoolDeserializer(),
            'array' => new ArrayDeserializer(new MixedValueDeserializer()),
            'object', 'Closure', 'stdClass' => new ObjectDeserializer(),
            'mixed' => new MixedValueDeserializer(),
            default => new UserDefinedObjectDeserializer(self::getReflectionFor($type))
        };
    }

    public static function fromReflectionNamedType(ReflectionType $type): Deserializer
    {
        if ($type instanceof ReflectionUnionType) {
            return self::fromReflectionUnionType($type);
        }

        if ($type instanceof ReflectionNamedType) {
            if ($type->isBuiltin()) {
                $deserializer = self::parse($type->getName());
            } else {
                $deserializer = new UserDefinedObjectDeserializer(self::getReflectionFor($type->getName()));
            }
        } else {
            $deserializer = new MixedValueDeserializer();
        }

        if ($type->allowsNull()) {
            return new DefaultValueDeserializer($deserializer);
        }

        return $deserializer;
    }

    private static function fromReflectionUnionType(ReflectionUnionType $type): Deserializer
    {
        /** @var Deserializer[] $deserializers */
        $deserializers = [];
        foreach ($type->getTypes() as $ty) {
            $deserializers[] = self::fromReflectionNamedType($ty);
        }

        return new ChainedDeserializer(...$deserializers);
    }

    /**
     * @param string $class
     *
     * @return ReflectionClass<object>
     * @throws ReflectionException
     */
    private static function getReflectionFor(string $class): ReflectionClass
    {
        assert(class_exists($class));

        return new ReflectionClass($class);
    }
}

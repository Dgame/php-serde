<?php

namespace Dgame\Serde\Deserializer;

use ReflectionClass;
use ReflectionNamedType;
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
            default => new UserDefinedObjectDeserializer(new ReflectionClass($type))
        };
    }

    public static function fromReflectionNamedType(ReflectionNamedType $type): Deserializer
    {
        if ($type instanceof ReflectionUnionType) {
            return self::fromReflectionUnionType($type);
        }

        if ($type->isBuiltin()) {
            $deserializer = self::parse($type->getName());
        } else {
            $deserializer = new UserDefinedObjectDeserializer(new ReflectionClass($type->getName()));
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
}

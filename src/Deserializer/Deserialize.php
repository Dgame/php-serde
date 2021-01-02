<?php

namespace Dgame\Serde\Deserializer;

use ReflectionClass;
use stdClass;

trait Deserialize
{
    private static ?UserDefinedObjectDeserializer $deserializer = null;

    public static function deserialize(stdClass $input): static
    {
        if (self::$deserializer === null) {
            self::$deserializer = new UserDefinedObjectDeserializer(new ReflectionClass(static::class));
        }

        return self::$deserializer->deserialize($input);
    }
}

<?php

namespace Dgame\Serde\Std;

use stdClass;

trait Deserialize
{
    public static function deserializeFromStdClass(stdClass $input): ?static
    {
        $deserializer = new Deserializer();

        return $deserializer->deserialize(static::class, $input);
    }
}

<?php

namespace Dgame\Serde\Json;

use stdClass;

trait Deserialize
{
    use \Dgame\Serde\Std\Deserialize;

    public static function deserializeFromJson(string $input): ?static
    {
        $object = json_decode($input, associative: false, flags: JSON_THROW_ON_ERROR);

        return $object instanceof stdClass ? static::deserializeFromStdClass($object) : null;
    }
}

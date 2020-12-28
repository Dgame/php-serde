<?php

namespace Dgame\Serde\Array;

trait Deserialize
{
    use \Dgame\Serde\Std\Deserialize;

    public static function deserializeFromArray(array $input): ?static
    {
        return static::deserializeFromStdClass((object) $input);
    }
}

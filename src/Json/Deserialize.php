<?php

declare(strict_types=1);

namespace Dgame\Serde\Json;

use stdClass;

trait Deserialize
{
    use \Dgame\Serde\Deserializer\Deserialize;

    public static function deserializeJson(string $content): ?static
    {
        $input = json_decode($content, associative: false, flags: JSON_THROW_ON_ERROR);

        return $input instanceof stdClass ? self::deserialize($input) : null;
    }
}

<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class BoolDeserializer implements Deserializer
{
    public function deserialize(mixed $input): bool
    {
        assert(is_numeric($input) || is_bool($input));

        return (bool) $input;
    }

    public function getDefaultValue(): bool
    {
        return false;
    }
}

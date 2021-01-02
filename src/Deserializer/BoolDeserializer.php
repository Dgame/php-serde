<?php

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

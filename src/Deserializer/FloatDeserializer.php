<?php

namespace Dgame\Serde\Deserializer;

final class FloatDeserializer implements Deserializer
{
    public function deserialize(mixed $input): float
    {
        assert(is_numeric($input) || is_float($input));

        return (float) $input;
    }

    public function getDefaultValue(): float
    {
        return 0.0;
    }
}

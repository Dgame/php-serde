<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class FloatDeserializer implements Deserializer
{
    public function deserialize(mixed $input): float
    {
        /** @phpstan-ignore-next-line */
        assert(is_numeric($input) || is_float($input));

        return (float) $input;
    }

    public function getDefaultValue(): float
    {
        return 0.0;
    }
}

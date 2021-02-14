<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class IntDeserializer implements Deserializer
{
    public function deserialize(mixed $input): int
    {
        assert(is_numeric($input));

        return (int) $input;
    }

    public function getDefaultValue(): int
    {
        return 0;
    }
}

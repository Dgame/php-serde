<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class StringDeserializer implements Deserializer
{
    public function deserialize(mixed $input): string
    {
        assert(is_string($input));

        return $input;
    }

    public function getDefaultValue(): string
    {
        return '';
    }
}

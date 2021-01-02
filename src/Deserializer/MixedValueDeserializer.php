<?php

namespace Dgame\Serde\Deserializer;

final class MixedValueDeserializer implements Deserializer
{
    public function deserialize(mixed $input): int
    {
        return $input;
    }

    public function getDefaultValue(): mixed
    {
        return null;
    }
}

<?php

namespace Dgame\Serde\Deserializer;

final class ObjectDeserializer implements Deserializer
{
    public function deserialize(mixed $input): object
    {
        assert(is_object($input));

        return $input;
    }

    public function getDefaultValue(): ?object
    {
        return null;
    }
}

<?php

namespace Dgame\Serde\Deserializer;

interface Deserializer
{
    public function deserialize(mixed $input): mixed;

    public function getDefaultValue(): mixed;
}


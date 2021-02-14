<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

interface Deserializer
{
    public function deserialize(mixed $input): mixed;

    public function getDefaultValue(): mixed;
}


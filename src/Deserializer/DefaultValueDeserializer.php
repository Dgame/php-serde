<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class DefaultValueDeserializer implements Deserializer
{
    public function __construct(private Deserializer $deserializer, private mixed $default = null)
    {
        $this->default ??= $this->deserializer->getDefaultValue();
    }

    public function deserialize(mixed $input): mixed
    {
        if ($input === null) {
            return $this->default;
        }

        return $this->deserializer->deserialize($input);
    }

    public function getDefaultValue(): mixed
    {
        return $this->default;
    }
}

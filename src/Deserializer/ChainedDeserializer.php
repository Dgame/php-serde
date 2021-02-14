<?php

declare(strict_types=1);

namespace Dgame\Serde\Deserializer;

final class ChainedDeserializer implements Deserializer
{
    /**
     * @var Deserializer[]
     */
    private array $deserializers;

    public function __construct(Deserializer ...$deserializers)
    {
        $this->deserializers = $deserializers;
    }

    public function getDefaultValue(): mixed
    {
        foreach ($this->deserializers as $deserializer) {
            $value = $deserializer->getDefaultValue();
            if ($value !== null) {
                return $value;
            }
        }

        return null;
    }

    public function deserialize(mixed $input): mixed
    {
        foreach ($this->deserializers as $deserializer) {
            $input = $deserializer->deserialize($input);
        }

        return $input;
    }
}

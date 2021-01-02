<?php

namespace Dgame\Serde\Deserializer;

use stdClass;

final class ArrayDeserializer implements Deserializer
{
    public function __construct(private Deserializer $deserializer)
    {
    }

    public function deserialize(mixed $input): array
    {
        if ($input instanceof stdClass) {
            $input = (array) $input;
        }

        assert(is_array($input), var_export($input, true));

        $output = [];
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $value = $this->deserialize($value);
            }

            $output[$key] = $this->deserializer->deserialize($value);
        }

        return $output;
    }

    public function getDefaultValue(): array
    {
        return [];
    }
}

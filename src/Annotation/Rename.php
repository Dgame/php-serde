<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rename implements PropertyAnnotation
{
    public function __construct(private ?string $serialize = null, private ?string $deserialize = null)
    {
    }

    public function apply(Meta $meta): void
    {
        if ($this->serialize !== null) {
            $meta->setSerializeAs($this->serialize);
        }

        if ($this->deserialize !== null) {
            $meta->setDeserializeAs($this->deserialize);
        }
    }
}

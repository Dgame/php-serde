<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Ignore implements PropertyAnnotation
{
    public function __construct(private bool $serialize = true, private bool $deserialize = true)
    {
    }

    public function apply(Meta $meta): void
    {
        $meta->setIgnoredBySerialize($this->serialize);
        $meta->setIgnoredByDeserialize($this->deserialize);
    }
}

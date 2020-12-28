<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class DefaultValue implements PropertyAnnotation
{
    public function __construct(private mixed $default)
    {
    }

    public function apply(Meta $meta): void
    {
        $meta->setDefault($this->default);
    }
}

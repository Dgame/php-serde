<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;
use ReflectionClass;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Template implements PropertyAnnotation
{
    public function __construct(private string $class)
    {
    }

    public function apply(Meta $meta): void
    {
        $meta->setReflectionClass(new ReflectionClass($this->class));
    }
}

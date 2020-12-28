<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Method implements PropertyAnnotation
{
    public function __construct(private ?string $setter = null, private ?string $getter = null)
    {
    }

    public function apply(Meta $meta): void
    {
        if ($this->setter !== null) {
            $meta->setSetter($this->setter);
        }

        if ($this->getter !== null) {
            $meta->setGetter($this->getter);
        }
    }
}

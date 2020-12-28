<?php

namespace Dgame\Serde\Annotation;

use Dgame\Serde\Meta;

interface PropertyAnnotation
{
    public function apply(Meta $meta): void;
}


<?php

namespace Dgame\Serde\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Rename
{
    public function __construct(public ?string $deserialize = null, public ?string $serialize = null)
    {
    }
}

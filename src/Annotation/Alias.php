<?php

declare(strict_types=1);

namespace Dgame\Serde\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Alias
{
    public function __construct(public string $alias)
    {
    }
}

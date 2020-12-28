<?php

namespace Dgame\Serde\Annotation;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
final class RenameAll
{
    public const PASCAL_CASE = 'PascalCase';

    public function __construct(private string $case) { }
}

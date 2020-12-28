<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY | Attribute::IS_REPEATABLE)]
final class Alias implements PropertyAnnotation
{
    /**
     * @var string[]
     */
    private array $alias;

    public function __construct(string ...$alias)
    {
        $this->alias = $alias;
    }

    public function apply(Meta $meta): void
    {
        $meta->setAlias(...$this->alias);
    }
}

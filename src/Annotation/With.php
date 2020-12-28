<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class With implements PropertyAnnotation
{
    /**
     * @var callable|null
     */
    private $serialize;
    /**
     * @var callable|null
     */
    private $deserialize;

    public function __construct(callable $serialize = null, callable $deserialize = null)
    {
        $this->serialize   = $serialize;
        $this->deserialize = $deserialize;
    }

    public function apply(Meta $meta): void
    {
        if ($this->serialize !== null) {
            $meta->setSerializeWith($this->serialize);
        }

        if ($this->deserialize !== null) {
            $meta->setDeserializeWith($this->deserialize);
        }
    }
}

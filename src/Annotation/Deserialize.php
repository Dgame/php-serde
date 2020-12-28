<?php

namespace Dgame\Serde\Annotation;

use Attribute;
use Dgame\Serde\Meta;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class Deserialize implements PropertyAnnotation
{
    /**
     * @var callable|null
     */
    private $with;

    public function __construct(private ?string $name = null, callable $with = null)
    {
        $this->with = $with;
    }

    public function apply(Meta $meta): void
    {
        if ($this->name !== null) {
            $meta->setDeserializeAs($this->name);
        }

        if ($this->with !== null) {
            $meta->setDeserializeWith($this->with);
        }
    }
}

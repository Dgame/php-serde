<?php

namespace Dgame\Serde;

use Dgame\Serde\Annotation\PropertyAnnotation;
use JetBrains\PhpStorm\Pure;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionProperty;

final class Meta
{
    private string $name;
    private ?Type $type = null;
    private ?ReflectionClass $reflection = null;
    private bool $optional = false;
    private mixed $default;
    private ?string $serializeAs = null;
    private ?string $deserializeAs = null;
    private bool $ignoredBySerialize = false;
    private bool $ignoredByDeserialize = false;
    private ?string $case = null;
    private ?string $getter = null;
    private ?string $setter = null;
    /**
     * @var string[]
     */
    private array $alias = [];
    /**
     * @var callable|null
     */
    private $serializeWith;
    /**
     * @var callable|null
     */
    private $deserializeWith;

    public function __construct(ReflectionProperty $property)
    {
        $this->name = $property->getName();
        /** @var ReflectionNamedType|null $type */
        $type = $property->getType();
        if ($type !== null) {
            if (!$type->isBuiltin()) {
                $this->reflection = new ReflectionClass($type->getName());
            }

            $this->type = Type::detect($type);
        }
        $this->default = $property->getDefaultValue();

        foreach ($property->getAttributes() as $attribute) {
            $annotation = $attribute->newInstance();
            if ($annotation instanceof PropertyAnnotation) {
                $annotation->apply($this);
            }
        }
    }

    public function getClassReflection(): ?ReflectionClass
    {
        return $this->reflection;
    }

    public function setReflectionClass(ReflectionClass $reflection): void
    {
        $this->reflection = $reflection;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isIgnoredBySerialize(): bool
    {
        return $this->ignoredBySerialize;
    }

    public function isIgnoredByDeserialize(): bool
    {
        return $this->ignoredByDeserialize;
    }

    public function setIgnoredByDeserialize(bool $ignoredByDeserialize): void
    {
        $this->ignoredByDeserialize = $ignoredByDeserialize;
    }

    public function setIgnoredBySerialize(bool $ignoredBySerialize): void
    {
        $this->ignoredBySerialize = $ignoredBySerialize;
    }

    public function getCase(): ?string
    {
        return $this->case;
    }

    public function setCase(?string $case): void
    {
        $this->case = $case;
    }

    public function getGetter(): ?string
    {
        return $this->getter;
    }

    public function setGetter(?string $getter): void
    {
        $this->getter = $getter;
    }

    public function getSetter(): ?string
    {
        return $this->setter;
    }

    public function setSetter(?string $setter): void
    {
        $this->setter = $setter;
    }

    #[Pure]
    public function isOptional(): bool
    {
        return $this->optional || $this->type === null || $this->type->isNullable() || $this->default !== null;
    }

    public function setOptional(bool $optional): void
    {
        $this->optional = $optional;
    }

    #[Pure]
    public function getDefaultValue(): mixed
    {
        if ($this->default === null && $this->type !== null) {
            return $this->type->default();
        }

        return $this->default;
    }

    public function setDefault(mixed $default): void
    {
        $this->default = $default;
    }

    /**
     * @return string[]
     */
    #[Pure]
    public function getAlias(): array
    {
        return $this->alias;
    }

    public function setAlias(string ...$alias): void
    {
        foreach ($alias as $name) {
            $this->alias[] = $name;
        }
    }

    /**
     * @return callable|null
     */
    #[Pure]
    public function getDeserializeWith(): ?callable
    {
        return $this->deserializeWith;
    }

    public function setDeserializeWith(callable $closure): void
    {
        $this->deserializeWith = $closure;
    }

    /**
     * @return callable|null
     */
    #[Pure]
    public function getSerializeWith(): ?callable
    {
        return $this->serializeWith;
    }

    public function setSerializeWith(callable $closure): void
    {
        $this->serializeWith = $closure;
    }

    #[Pure]
    public function getSerializeAs(): ?string
    {
        return $this->serializeAs;
    }

    public function setSerializeAs(string $serializeAs): void
    {
        $this->serializeAs = $serializeAs;
    }

    #[Pure]
    public function getDeserializeAs(): ?string
    {
        return $this->deserializeAs;
    }

    public function setDeserializeAs(string $deserializeAs): void
    {
        $this->deserializeAs = $deserializeAs;
    }
}

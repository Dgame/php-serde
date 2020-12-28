<?php

namespace Dgame\Serde;

use JetBrains\PhpStorm\Pure;
use ReflectionNamedType;
use ReflectionUnionType;

final class Type
{
    private const IS_INT          = 1 << 0;
    private const IS_STRING       = 1 << 1;
    private const IS_BOOL         = 1 << 2;
    private const IS_FLOAT        = 1 << 3;
    private const IS_ARRAY        = 1 << 4;
    private const IS_CALLABLE     = 1 << 5;
    private const IS_OBJECT       = 1 << 6;
    private const IS_USER_DEFINED = 1 << 7;
    private const IS_MIXED        = 1 << 8;
    private const IS_NULLABLE     = 1 << 9;

    private function __construct(private int $type)
    {
    }

    public function default(): mixed
    {
        return match ($this->type) {
            self::IS_INT => 0,
            self::IS_STRING => '',
            self::IS_BOOL => false,
            self::IS_FLOAT => 0.0,
            self::IS_ARRAY => [],
            default => null,
        };
    }

    public function isNullable(): bool
    {
        return ($this->type & self::IS_NULLABLE) !== 0;
    }

    #[Pure]
    public function isObject(): bool
    {
        return match ($this->type) {
            self::IS_USER_DEFINED, self::IS_OBJECT => true,
            default => false
        };
    }

    public static function union(ReflectionUnionType $unionType): self
    {
        $result = $unionType->allowsNull() ? self::IS_NULLABLE : 0;
        foreach ($unionType->getTypes() as $type) {
            $result |= self::detect($type)->type;
        }

        return new self($result);
    }

    public static function detect(ReflectionNamedType $type): self
    {
        if ($type instanceof ReflectionUnionType) {
            return self::union($type);
        }

        $result = match ($type->getName()) {
            'int' => self::IS_INT,
            'string' => self::IS_STRING,
            'bool' => self::IS_BOOL,
            'float' => self::IS_FLOAT,
            'array' => self::IS_ARRAY,
            'object' => self::IS_OBJECT,
            'callable' => self::IS_CALLABLE,
            default => self::IS_MIXED,
        };

        if ($type->allowsNull()) {
            $result |= self::IS_NULLABLE;
        }

        return new self($result);
    }
}

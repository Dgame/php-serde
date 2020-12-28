<?php

//
//namespace Dgame\Serde\Array {
//
//    use Dgame\Serde\Annotation\Alias;
//    use Dgame\Serde\Annotation\Template;
//
//    trait Deserialize
//    {
//        use \Dgame\Serde\Std\Deserialize;
//
//        public static function deserializeFromArray(array $input): ?static
//        {
//            return static::deserializeFromStdClass((object) $input);
//        }
//    }
//
//    trait Serialize
//    {
//        public function serializeIntoArray(): array
//        {
//            return [];
//        }
//    }
//
//    function to_int($value): int
//    {
//        assert(is_numeric($value));
//
//        return (int) $value;
//    }
//
//    $ti = static fn($value) => (int) $value;
//
//    final class Foo
//    {
//        use Deserialize;
//
//        #[Alias('ID')]
//        #[Alias('Id')]
//        //        #[With(deserialize: 'to_int')]
//            //        #[Deserialize(with: 'to_int')]
//        public int $id;
//
//        public function __construct(int $id)
//        {
//        }
//    }
//
//    print_r(Foo::deserializeFromArray(['id' => 42]));
//    print_r(Foo::deserializeFromArray(['ID' => 42]));
//    print_r(Foo::deserializeFromArray(['Id' => 42]));
//
//    //    print_r(Foo::fromArray(['Id' => '23']));
//
//    final class Bar
//    {
//        use Deserialize;
//
//        #[Template(class: Foo::class)]
//        public array $fs;
//    }
//
//    print_r(Bar::deserializeFromArray(['fs' => [new Foo(1)]]));
//    print_r(Bar::deserializeFromArray(['fs' => [(object) ['Id' => 2]]]));
//
//    final class Quatz
//    {
//        use Deserialize;
//
//        #[Template(class: Foo::class)]
//        public array $fs;
//    }
//
//    print_r(Quatz::deserializeFromArray(['fs' => ['a' => new Foo(1)]]));
//    print_r(Quatz::deserializeFromArray(['fs' => ['a' => (object) ['Id' => 2]]]));
//}
//
//namespace Dgame\Serde\Std {
//
//    use Dgame\Serde\Annotation\Annotation;
//    use Dgame\Serde\Meta;
//    use JetBrains\PhpStorm\Pure;
//    use ReflectionClass;
//    use ReflectionException;
//    use ReflectionProperty;
//    use RuntimeException;
//    use stdClass;
//
//    final class Deserializer
//    {
//        /**
//         * @param string|object $class
//         * @param mixed         $input
//         *
//         * @return object|null
//         * @throws ReflectionException
//         */
//        public function deserialize(string|object $class, stdClass $input): ?object
//        {
//            $refl = new ReflectionClass($class);
//
//            return $this->hydrate(is_object($class) ? $class : null, $refl, $input);
//        }
//
//        /**
//         * @param object|null     $object
//         * @param ReflectionClass $reflection
//         * @param stdClass        $input
//         *
//         * @return object|null
//         * @throws ReflectionException
//         */
//        private function hydrate(?object $object, ReflectionClass $reflection, stdClass $input): ?object
//        {
//            $object ??= $this->createObject($reflection);
//            foreach ($reflection->getProperties() as $property) {
//                $property->setAccessible(true);
//
//                $meta = $this->collectMetaDataFrom($property);
//                $name = $this->findPropertyNameIn($meta, $input);
//                if ($name === null) {
//                    $this->applyDefaultValue($meta, $object, $property);
//
//                    continue;
//                }
//
//                $value = $input->{$name};
//                if (($reflection = $meta->getClassReflection()) !== null) {
//                    $value = match (true) {
//                        $this->isSingleObject($value) => $this->hydrate(null, $reflection, $value),
//                        $this->isArrayOfObjects($value) => $this->hydrateAll($value, $reflection),
//                        default => $value
//                    };
//                }
//
//                if (($closure = $meta->getDeserializeWith()) !== null) {
//                    $value = $closure($value);
//                }
//
//                $property->setValue($object, $value);
//            }
//
//            return $object;
//        }
//
//        /**
//         * @param ReflectionClass $reflection
//         *
//         * @return object
//         * @throws ReflectionException
//         */
//        private function createObject(ReflectionClass $reflection): object
//        {
//            $ctor = $reflection->getConstructor();
//            if ($ctor !== null && $ctor->getNumberOfRequiredParameters() !== 0) {
//                return $reflection->newInstanceWithoutConstructor();
//            }
//
//            return $reflection->newInstance();
//        }
//
//        private function collectMetaDataFrom(ReflectionProperty $property): Meta
//        {
//            $meta = new Meta($property);
//            foreach ($property->getAttributes() as $attribute) {
//                $annotation = $attribute->newInstance();
//                if ($annotation instanceof Annotation) {
//                    $annotation->apply($meta);
//                }
//            }
//
//            return $meta;
//        }
//
//        private function findPropertyNameIn(Meta $meta, stdClass $object): ?string
//        {
//            $alias = [
//                $meta->getName(),
//                $meta->getSerializeAs(),
//                $meta->getDeserializeAs(),
//                ...$meta->getAlias()
//            ];
//            foreach ($alias as $name) {
//                if (property_exists($object, $name)) {
//                    return $name;
//                }
//            }
//
//            return null;
//        }
//
//        private function applyDefaultValue(Meta $meta, object $object, ReflectionProperty $property): void
//        {
//            if ($meta->isOptional()) {
//                $property->setValue($object, $meta->getDefaultValue());
//
//                return;
//            }
//
//            throw new RuntimeException('Not optional value for ' . $property->getName());
//        }
//
//        #[Pure]
//        private function isSingleObject(mixed $value): bool
//        {
//            return $value instanceof stdClass;
//        }
//
//        #[Pure]
//        private function isArrayOfObjects(mixed $value): bool
//        {
//            if (!is_array($value)) {
//                return false;
//            }
//
//            $first = current($value);
//
//            return $first instanceof stdClass;
//        }
//
//        /**
//         * @param array           $objects
//         * @param ReflectionClass $reflection
//         *
//         * @return array
//         * @throws ReflectionException
//         */
//        private function hydrateAll(array $objects, ReflectionClass $reflection): array
//        {
//            $output = [];
//            foreach ($objects as $key => $object) {
//                $output[$key] = $this->hydrate(null, $reflection, $object);
//            }
//
//            return $output;
//        }
//    }
//
//    trait Deserialize
//    {
//        public static function deserializeFromStdClass(stdClass $input): ?static
//        {
//            $deserializer = new Deserializer();
//
//            return $deserializer->deserialize(static::class, $input);
//        }
//    }
//
//    trait Serialize
//    {
//        use \Dgame\Serde\Array\Serialize;
//
//        public function serializeIntoStdClass(): stdClass
//        {
//            return (object) $this->serializeIntoArray();
//        }
//    }
//}
//
//namespace Dgame\Serde\Json {
//
//    use stdClass;
//
//    trait Deserialize
//    {
//        use \Dgame\Serde\Std\Deserialize;
//
//        public static function deserializeFromJson(string $input): ?static
//        {
//            $object = json_decode($input, associative: false, flags: JSON_THROW_ON_ERROR);
//
//            return $object instanceof stdClass ? static::deserializeFromStdClass($object) : null;
//        }
//    }
//
//    trait Serialize
//    {
//        public function serializeIntoJson(): array
//        {
//            return [];
//        }
//    }
//
//    final class A
//    {
//        use Deserialize;
//
//        private int $id = 23;
//    }
//
//    print_r(A::deserializeFromJson('{ "id": 42 }'));
//    print_r(A::deserializeFromJson('{ }'));
//
//    final class B
//    {
//        use Deserialize;
//
//        private ?int $id = null;
//    }
//
//    print_r(B::deserializeFromJson('{ "id": 42 }'));
//    print_r(B::deserializeFromJson('{ }'));
//}

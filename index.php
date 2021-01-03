<?php

require_once 'vendor/autoload.php';

use Dgame\Serde\Annotation\Alias;
use Dgame\Serde\Annotation\ArrayOf;
use Dgame\Serde\Annotation\DefaultValue;
use Dgame\Serde\Annotation\Rename;
use Dgame\Serde\Deserializer\ArrayDeserializer;
use Dgame\Serde\Deserializer\DefaultValueDeserializer;
use Dgame\Serde\Deserializer\IntDeserializer;
use Dgame\Serde\Json\Deserialize;
use Dgame\Serde\Deserializer\UserDefinedObjectDeserializer;

final class A
{
    private int $id;
}

$udoa = new UserDefinedObjectDeserializer(new ReflectionClass(A::class));
$udoa->setAlias('Id', 'id');
$udoa->setAlias('ID', 'id');
$udoa->setDeserializer('id', new DefaultValueDeserializer(new IntDeserializer(), default: 1337));
print_r($udoa->deserialize((object) []));
print_r($udoa->deserialize((object) ['id' => 1]));
print_r($udoa->deserialize((object) ['Id' => 2]));
print_r($udoa->deserialize((object) ['ID' => 3]));

final class B
{
    private array $as = [1, 2, 3];
}

$udob = new UserDefinedObjectDeserializer(new ReflectionClass(B::class));
$udob->setDeserializer('as', new DefaultValueDeserializer(new ArrayDeserializer($udoa)));
print_r($udob->deserialize((object) []));
print_r($udob->deserialize((object) ['as' => []]));
print_r($udob->deserialize((object) ['as' => [(object) ['id' => 1]]]));
print_r($udob->deserialize((object) ['as' => [(object) ['id' => 1], (object) ['Id' => 2]]]));
print_r($udob->deserialize((object) ['as' => ['a' => (object) ['id' => 1], 'b' => (object) ['Id' => 2]]]));

final class C
{
    use Deserialize;

    #[DefaultValue(1337)]
    #[Alias("Id")]
    #[Alias("ID")]
    private int $id;
}

print_r(C::deserialize((object) []));
print_r(C::deserialize((object) ['id' => 1]));
print_r(C::deserialize((object) ['Id' => 2]));
print_r(C::deserialize((object) ['ID' => 3]));

print_r(C::deserializeJson('{ }'));
print_r(C::deserializeJson('{ "id": 1 }'));
print_r(C::deserializeJson('{ "Id": 2 }'));
print_r(C::deserializeJson('{ "ID": 3 }'));

final class D
{
    use Deserialize;

    #[ArrayOf(C::class)]
    #[DefaultValue]
    private array $children = [1, 2, 3];
}

print_r(D::deserializeJson('{ }'));
print_r(D::deserializeJson('{ "children": [] }'));
print_r(D::deserializeJson('{ "children": [{ "Id": 1 }] }'));
print_r(D::deserializeJson('{ "children": [{ "Id": 2 }, { "Id": 3 }] }'));
print_r(D::deserializeJson('{ "children": {"a": { "Id": 4 }, "b": { "ID": 5 }} }'));

final class E {
    use Deserialize;

    #[Rename(deserialize: "id")]
    #[DefaultValue(42)]
    #[Alias("Id")]
    #[Alias("ID")]
    public int $myId;
}

print_r(E::deserializeJson('{ }'));
print_r(E::deserializeJson('{ "id": 1 }'));
print_r(E::deserializeJson('{ "Id": 2 }'));
print_r(E::deserializeJson('{ "ID": 3 }'));

<?php

namespace Dgame\Serde\Std;

use stdClass;

trait Serialize
{
    use \Dgame\Serde\Array\Serialize;

    public function serializeIntoStdClass(): stdClass
    {
        return (object) $this->serializeIntoArray();
    }
}

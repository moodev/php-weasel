<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;

class FloatType implements Type
{

    public function decodeValue($value, JsonMapper $mapper)
    {
        if (!is_numeric($value)) {
            throw new \Exception("Type error, expected numeric but got " . $value);
        }
        return (float)$value;
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        if (!is_float($value)) {
            throw new \Exception("Type error");
        }
        return (float)$value;
    }

}

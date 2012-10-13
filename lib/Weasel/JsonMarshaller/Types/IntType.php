<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;

class IntType implements Type
{

    public function decodeValue($value, JsonMapper $mapper)
    {
        if (!is_numeric($value)) {
            throw new \Exception("Type error, expected numeric but got " . $value);
        }
        return (int)$value;
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        if (!is_int($value)) {
            throw new \Exception("Type error");
        }
        return (int)$value;
    }

}

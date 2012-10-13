<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;

class StringType implements Type
{

    public function decodeValue($value, JsonMapper $mapper)
    {
        if (!is_string($value)) {
            throw new \Exception("Type error, expected string but got " . gettype($value));
        }
        return (string)$value;
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        if (!is_string($value)) {
            throw new \Exception("Type error");
        }
        return (string)$value;
    }

}

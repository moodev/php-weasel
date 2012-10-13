<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class StringType implements Type
{

    protected function checkAndCastValue($value)
    {
        if (!is_string($value)) {
            throw new InvalidTypeException("string", $value);
        }
        return (string)$value;
    }

    public function decodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCastValue($value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCastValue($value);
    }

}

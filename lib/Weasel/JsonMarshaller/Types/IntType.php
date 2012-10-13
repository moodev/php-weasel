<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class IntType implements Type
{

    protected function checkAndCastValue($value)
    {
        if (!is_int($value) && !ctype_digit($value)) {
            throw new InvalidTypeException("integer", $value);
        }
        return (int)$value;
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

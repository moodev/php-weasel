<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class FloatType implements Type
{

    protected function checkAndCast($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidTypeException("float", $value);
        }
        return (float)($value + 0);
    }

    public function decodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCast($value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCast($value);
    }

}

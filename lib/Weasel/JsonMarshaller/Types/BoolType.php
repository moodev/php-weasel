<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class BoolType implements Type
{

    public function decodeValue($value, JsonMapper $mapper)
    {
        if (is_bool($value)) {
            return (bool)$value;
        }
        if ($value === "true" || $value === 1) {
            return true;
        }
        if ($value === "false" || $value === 0) {
            return false;
        }
        throw new InvalidTypeException("boolean", $value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        if (!is_bool($value)) {
            throw new InvalidTypeException("boolean", $value);
        }
        return (bool)$value;
    }

}

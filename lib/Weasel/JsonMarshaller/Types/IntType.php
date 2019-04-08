<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class IntType implements JsonType
{

    protected function checkAndCastValue($value)
    {
        if (!is_numeric($value)) {
            throw new InvalidTypeException("integer", $value);
        }
        $intval = (int)($value + 0);
        if ($intval != $value) {
            throw new InvalidTypeException("integer", $value);
        }
        return $intval;
    }

    public function decodeValue($value, JsonMapper $mapper, $strict)
    {
        if ($strict) {
            if (!is_int($value)) {
                throw new InvalidTypeException("integer", $value);
            }
        }
        return $this->checkAndCastValue($value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return json_encode($this->checkAndCastValue($value));
    }

}

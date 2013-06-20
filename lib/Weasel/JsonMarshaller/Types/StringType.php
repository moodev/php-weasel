<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class StringType implements JsonType
{

    protected function checkAndCastValue($value)
    {
        return (string)$value;
    }

    public function decodeValue($value, JsonMapper $mapper)
    {
        return $this->checkAndCastValue($value);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return json_encode($this->checkAndCastValue($value));
    }

}

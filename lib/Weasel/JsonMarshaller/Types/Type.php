<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

interface Type
{
    /**
     * Deserialize something to its PHP type.
     * @param mixed $value
     * @param \Weasel\JsonMarshaller\JsonMapper $mapper
     * @return mixed
     */
    public function decodeValue($value, JsonMapper $mapper);

    /**
     * Serialize a PHP value.
     * @param $value
     * @param \Weasel\JsonMarshaller\JsonMapper $mapper
     * @return mixed
     */
    public function encodeValue($value, JsonMapper $mapper);

}

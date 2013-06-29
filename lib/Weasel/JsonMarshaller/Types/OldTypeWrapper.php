<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\JsonMapper;

class OldTypeWrapper implements JsonType
{

    protected $_oldType;

    public function __construct(Type $oldType)
    {
        $this->_oldType = $oldType;
    }

    public function decodeValue($value, JsonMapper $mapper, $strict)
    {
        return $this->_oldType->decodeValue($value, $mapper);
    }

    public function encodeValue($value, JsonMapper $mapper)
    {
        return json_encode($this->_oldType->encodeValue($value, $mapper));
    }
}

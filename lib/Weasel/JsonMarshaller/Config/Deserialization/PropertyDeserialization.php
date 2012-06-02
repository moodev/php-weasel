<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

abstract class PropertyDeserialization
{

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     */
    public $typeInfo;
}

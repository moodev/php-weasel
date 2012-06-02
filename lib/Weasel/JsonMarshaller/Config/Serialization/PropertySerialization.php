<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

class PropertySerialization
{

    /**
     * @var integer
     */
    public $include;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \Weasel\JsonMarshaller\Config\Serialization\TypeInfo
     */
    public $typeInfo;
}

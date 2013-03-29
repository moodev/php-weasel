<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Serialization;

class GetterSerialization extends PropertySerialization
{

    /**
     * @var string
     */
    public $method;

    function __construct($method = null, $type = null, $include = null, $typeInfo = null)
    {
        $this->method = $method;
        $this->include = $include;
        $this->type = $type;
        $this->typeInfo = $typeInfo;
    }

    public function __toString()
    {
        return "[GetterSerialization method={$this->method} include={$this->include} type={$this->type} typeInfo={$this->typeInfo}]";
    }


}

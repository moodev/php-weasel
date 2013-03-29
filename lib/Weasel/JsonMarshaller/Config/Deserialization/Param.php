<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

class Param extends PropertyDeserialization
{

    /**
     * @var string
     */
    public $name;

    public function __toString()
    {
        return "[ParamDeserialization name={$this->name} type={$this->type} typeInfo={$this->typeInfo}]";
    }

}

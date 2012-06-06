<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Deserialization;

class ElementWrapper
{


    /**
     * @var bool
     */
    public $nillable = false;


    /**
     * @var \Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization
     */
    public $wraps;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

}

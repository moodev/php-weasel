<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
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

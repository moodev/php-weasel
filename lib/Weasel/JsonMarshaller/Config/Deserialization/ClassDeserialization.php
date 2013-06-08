<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Deserialization;

use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonProperty;

class ClassDeserialization
{

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\PropertyDeserialization[]
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\PropertyDeserialization[string]")
     */
    public $properties = array();

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\Creator
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\Creator")
     */
    public $creator = null;

    /**
     * @var bool
     * @JsonProperty(type="bool")
     */
    public $ignoreUnknown;

    /**
     * @var string[]
     * @JsonProperty(type="string[]")
     */
    public $ignoreProperties;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $name;

    /**
     * @var \Weasel\JsonMarshaller\Config\Deserialization\TypeInfo
     * @JsonProperty(type="\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo")
     */
    public $typeInfo;

    /**
     * @var string
     * @JsonProperty(type="string")
     */
    public $anySetter = null;


    function __construct($name = null, $properties = array(), $anySetter = null, $creator = null, $ignoreProperties = null, $ignoreUnknown = null, $typeInfo = null)
    {
        $this->anySetter = $anySetter;
        $this->creator = $creator;
        $this->ignoreProperties = $ignoreProperties;
        $this->ignoreUnknown = $ignoreUnknown;
        $this->name = $name;
        $this->properties = $properties;
        $this->typeInfo = $typeInfo;
    }

    public function __toString()
    {
        $props = array();
        foreach ($this->properties as $key => $value) {
            $props[] = $key . ' => ' . $value;
        }
        return '[ClassDeserialization name=' . $this->name . ' ignoreProperties={' . implode(', ',
            $this->ignoreProperties) . '} anySetter=' . $this->anySetter .
        ' ignoreUnknown=' . ($this->ignoreUnknown ? "true" : "false") . ' typeInfo=' . $this->typeInfo . ' creator=' . $this->creator .
        ' properties={' . implode(", ", $props) . '}';
    }
}

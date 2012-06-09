<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Config;

class Annotation
{

    /**
     * @var string
     */
    private $class;

    /**
     * @var string[]
     */
    private $on;

    /**
     * @var int
     */
    private $max;

    /**
     * @var string
     */
    private $creatorMethod;

    /**
     * @var \Weasel\Annotation\Config\Param[]
     */
    private $creatorParams;

    /**
     * @var \Weasel\Annotation\Config\Property[]
     */
    private $properties;

    /**
     * @var \Weasel\Annotation\Config\Enum[]
     */
    private $enums;

    /**
     * @param string $class
     * @param string[] $on
     * @param int $max
     */
    public function __construct($class, $on, $max = null)
    {
        $this->class = $class;
        $this->on = $on;
        $this->max = $max;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getCreatorMethod()
    {
        return $this->creatorMethod;
    }

    /**
     * @return \Weasel\Annotation\Config\Param[]
     */
    public function getCreatorParams()
    {
        return $this->creatorParams;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return \string[]
     */
    public function getOn()
    {
        return $this->on;
    }

    /**
     * @param string $creatorMethod
     * @return \Weasel\Annotation\Config\Annotation
     */
    public function setCreatorMethod($creatorMethod)
    {
        $this->creatorMethod = $creatorMethod;
        return $this;
    }

    /**
     * @param \Weasel\Annotation\Config\Param $param
     * @return Annotation
     */
    public function addCreatorParam($param)
    {
        $this->creatorParams[] = $param;
        return $this;
    }

    /**
     * @param \Weasel\Annotation\Config\Param[] $creatorParams
     * @return Annotation
     */
    public function setCreatorParams($creatorParams)
    {
        $this->creatorParams = $creatorParams;
        return $this;
    }

    /**
     * @param int $max
     * @return \Weasel\Annotation\Config\Annotation
     */
    public function setMax($max)
    {
        $this->max = $max;
        return $this;
    }

    /**
     * @param $on
     * @return Annotation
     */
    public function setOn($on)
    {
        $this->on = $on;
        return $this;
    }

    /**
     * @return \Weasel\Annotation\Config\Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param \Weasel\Annotation\Config\Property[] $properties
     * @return Annotation
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
        return $this;
    }

    /**
     * @param \Weasel\Annotation\Config\Property $property
     * @return Annotation
     */
    public function addProperty(\Weasel\Annotation\Config\Property $property)
    {
        $this->properties[$property->getName()] = $property;
        return $this;
    }

    /**
     * @return \Weasel\Annotation\Config\Enum[]
     */
    public function getEnums()
    {
        return $this->enums;
    }

    /**
     * @param \Weasel\Annotation\Config\Enum[] $enums
     * @return Annotation
     */
    public function setEnums($enums)
    {
        $this->enums = $enums;
        return $this;
    }

    /**
     * @param \Weasel\Annotation\Config\Enum $enum
     * @return Annotation
     */
    public function addEnum($enum)
    {
        $this->enums[$enum->getName()] = $enum;
        return $this;
    }

    /**
     * @param string $name
     * @return \Weasel\Annotation\Config\Enum
     */
    public function getEnum($name)
    {
        return isset($this->enums[$name]) ? $this->enums[$name] : null;
    }

}


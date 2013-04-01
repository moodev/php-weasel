<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlElement;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class XmlElement implements IXmlElement
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type = "string";

    /**
     * @var string
     */
    public $namespace;

    /**
     * @var bool
     */
    public $required = false;

    /**
     * @var bool
     */
    public $nillable = false;

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getNillable()
    {
        return $this->nillable;
    }

}


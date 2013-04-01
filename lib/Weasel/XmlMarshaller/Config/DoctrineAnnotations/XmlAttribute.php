<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlAttribute;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class XmlAttribute implements IXmlAttribute
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

}


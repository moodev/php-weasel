<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlRootElement;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class XmlRootElement implements IXmlRootElement
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $namespace;

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

}


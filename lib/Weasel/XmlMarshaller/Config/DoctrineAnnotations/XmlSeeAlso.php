<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlSeeAlso;
use Weasel\Common\Utils\NoUndeclaredProperties;

/**
 * The list of subtypes of this base class
 * @Annotation
 * @Target({"CLASS"})
 */
class XmlSeeAlso extends NoUndeclaredProperties implements IXmlSeeAlso
{

    /**
     * @var array
     */
    public $value;

    /**
     * @return string[]
     */
    public function getValue()
    {
        return $this->value;
    }

}


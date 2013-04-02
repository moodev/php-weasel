<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlDiscriminator;
use Weasel\Common\Utils\NoUndeclaredProperties;

/**
 * @Annotation
 * @Target({"CLASS"})
 */
class XmlDiscriminator extends NoUndeclaredProperties implements IXmlDiscriminator
{

    /**
     * @var string
     */
    public $value;

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


}


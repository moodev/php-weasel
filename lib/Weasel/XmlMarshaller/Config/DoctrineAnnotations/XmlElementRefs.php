<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\DoctrineAnnotations;

use Doctrine\Common\Annotations\Annotation;
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlElementRefs;
use Weasel\Common\Utils\NoUndeclaredProperties;

/**
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
class XmlElementRefs extends NoUndeclaredProperties implements IXmlElementRefs
{

    /**
     * @var array
     */
    public $values;

    /**
     * @var string
     */
    public $type;

    public function getValues()
    {
        return $this->values;
    }

    public function getType()
    {
        return $this->type;
    }

}


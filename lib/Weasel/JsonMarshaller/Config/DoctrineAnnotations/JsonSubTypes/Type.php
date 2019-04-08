<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonSubTypes;

use Doctrine\Common\Annotations\Annotation;
use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\JsonSubTypes\IType;

/**
 * A subtype
 * @Annotation
 * @Target("ANNOTATION")
 */
class Type extends NoUndeclaredProperties implements IType
{

    /**
     * @var string
     */
    public $value;

    /**
     * @var string
     */
    public $name;


    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}


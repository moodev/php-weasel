<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonInclude;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class JsonInclude extends NoUndeclaredProperties implements IJsonInclude
{

    /**
     * @var integer
     * @Enum({\Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude::INCLUDE_ALWAYS,
     *        \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude::INCLUDE_NON_DEFAULT,
     *        \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude::INCLUDE_NON_EMPTY,
     *        \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonInclude::INCLUDE_NON_NULL
     *       })
     */
    public $value;

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

}

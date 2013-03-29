<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class JsonInclude extends NoUndeclaredProperties
{

    const INCLUDE_ALWAYS = 1;
    const INCLUDE_NON_DEFAULT = 2;
    const INCLUDE_NON_EMPTY = 3;
    const INCLUDE_NON_NULL = 4;

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

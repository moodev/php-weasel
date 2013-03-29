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
 * Allows you to specify an explicit name for this implementation, for use by JsonTypeInfo
 * @Annotation
 * @Target("CLASS")
 */
class JsonTypeName extends NoUndeclaredProperties
{

    /**
     * @var string
     */
    public $name;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

}


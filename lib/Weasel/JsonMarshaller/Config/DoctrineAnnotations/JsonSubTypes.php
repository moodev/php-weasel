<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Doctrine\Common\Annotations\Annotation;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonSubTypes;

/**
 * The list of subtypes of this base class
 * @Annotation
 * @Target({"CLASS", "METHOD", "PROPERTY"})
 */
class JsonSubTypes extends NoUndeclaredProperties implements IJsonSubTypes
{

    /**
     * @var array
     */
    public $value;

    /**
     * @return JsonSubTypes\Type[]
     */
    public function getValue()
    {
        return $this->value;
    }

}


<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\DoctrineAnnotations;

use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonProperty;

/**
 * Sets a property up to be serialized/deserialized explicitly.
 * The name sets the json field name to use for this property.
 * The type specifies the type to use.
 * Because PHP is not strongly typed we can only make best guesses about types if you do not provide type info!
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD", "ANNOTATION"})
 */
class JsonProperty extends NoUndeclaredProperties implements IJsonProperty
{

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function __construct($value)
    {
        if (isset($value["name"])) {
            $this->name = $value["name"];
        }
        if (isset($value["type"])) {
            $this->type = $value["type"];
        }
    }

}


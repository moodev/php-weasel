<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;
use Weasel\Common\Utils\NoUndeclaredProperties;
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonProperty;

/**
 * Sets a property up to be serialized/deserialized explicitly.
 * The name sets the json field name to use for this property.
 * The type specifies the type to use.
 * Because PHP is not strongly typed we can only make best guesses about types if you do not provide type info!
 * @Annotation(on={"property", "method", "\Weasel\JsonMarshaller\Config\Annotations\JsonCreator"})
 */
class JsonProperty extends NoUndeclaredProperties implements IJsonProperty
{

    protected $name;
    protected $type;
    protected $strict;

    /**
     * @param string $name
     * @param string $type
     * @param bool $strict
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="type", type="string", required=false), @Parameter(name="strict", type="bool", required=false)})
     */
    public function __construct($name, $type, $strict)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($type) ? $type : "string";
        $this->strict = isset($strict) ? $strict : true;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getStrict()
    {
        return $this->strict;
    }

}


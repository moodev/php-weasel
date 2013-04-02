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
use Weasel\JsonMarshaller\Config\IAnnotations\IJsonSubTypes;

/**
 * The list of subtypes of this base class
 * @Annotation(on={"class", "method", "property"})
 */
class JsonSubTypes extends NoUndeclaredProperties implements IJsonSubTypes
{

    /**
     * @var JsonSubTypes\Type[]
     */
    protected $value;

    /**
     * @param \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type[] $value
     * @AnnotationCreator(@Parameter(name="value", type="\Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type[]", required=true))
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return JsonSubTypes\Type[]
     */
    public function getValue()
    {
        return $this->value;
    }

}


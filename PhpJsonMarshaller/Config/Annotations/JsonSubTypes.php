<?php
namespace PhpJsonMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * The list of subtypes of this base class
 * @Annotation(on={"class", "method", "property"})
 */
class JsonSubTypes
{

    /**
     * @var JsonSubTypes\Type[]
     */
    protected $value;

    /**
     * @param \PhpJsonMarshaller\Config\Annotations\JsonSubTypes\Type[] $value
     * @AnnotationCreator(@Parameter(name="value", type="\PhpJsonMarshaller\Config\Annotations\JsonSubTypes\Type[]", required=true))
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


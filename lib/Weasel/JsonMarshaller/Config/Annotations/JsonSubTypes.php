<?php
namespace Weasel\JsonMarshaller\Config\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

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
     * @param \Weasel\JsonMarshaller\Config\Annotations\JsonSubTypes\Type[] $value
     * @AnnotationCreator(@Parameter(name="value", type="\JsonMarshaller\Config\Annotations\JsonSubTypes\Type[]", required=true))
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


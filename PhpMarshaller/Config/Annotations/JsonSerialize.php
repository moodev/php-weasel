<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * Allow serialization of a property as a different type to its actual type. Allows you to serialize
 * as e.g. a superclass.
 * The single property, as, must be the fully qualified class name to serialize as.
 * @Annotation(on={"property", "method"})
 */
class JsonSerialize
{

    protected $as;

    /**
     * @param string $as
     * @AnnotationCreator(@Parameter(name="as", type="string", required=true))
     */
    public function __construct($as)
    {
        $this->as = $as;
    }

    public function getAs()
    {
        return $this->as;
    }

}

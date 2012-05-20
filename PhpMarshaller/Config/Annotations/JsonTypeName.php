<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * Allows you to specify an explicit name for this implementation, for use by JsonTypeInfo
 * @Annotation(on={"class"})
 */
class JsonTypeName
{

    protected $name;

    /**
     * @param string $name
     * @AnnotationCreator(@Parameter(name="name", type="string", required=true))
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

}


<?php
namespace PhpAnnotation\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="property")
 */
class Enum
{

    protected $name;

    /**
     * @param string $name
     * @AnnotationCreator(@Parameter(name="name", type="string", required=false))
     */
    public function __construct($name)
    {
        $this->name = isset($name) ? $name : null;
    }

    public function getName()
    {
        return $this->name;
    }

}


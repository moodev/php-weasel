<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on={"property", "method", "\PhpMarshaller\Config\Annotations\JsonCreator})
 */
class JsonProperty
{

    protected $name;
    protected $type;

    /**
     * @param string $name
     * @param string $type
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="type", type="string", required=false)})
     */
    public function __construct($name, $type)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($name) ? $name : "string";
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

}


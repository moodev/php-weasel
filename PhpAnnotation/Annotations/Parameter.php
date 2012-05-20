<?php
namespace PhpAnnotation\Annotations;

/**
 * @Annotation(on="\PhpAnnotation\Annotations\AnnotationCreator")
 */
class Parameter
{

    protected $name;
    protected $type;
    protected $required;

    /**
     * @param string $name
     * @param string $type
     * @param bool $required
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="type", type="string", required=false), @Parameter(name="required", type="boolean", required=false)})
     */
    public function __construct($name, $type, $required)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($type) ? $type : "string";
        $this->name = isset($required) ? $required : false;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getType()
    {
        return $this->type;
    }

}

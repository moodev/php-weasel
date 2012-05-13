<?php
namespace PhpAnnotation\Annotations;

/**
 * @Annotation(on="\PhpAnnotation\Annotations\AnnotationCreator")
 */
class Parameter
{

    protected $name = null;
    protected $type = "string";
    protected $required = false;

    /**
     * @param string $name
     * @param string $type
     * @param bool $required
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="type", type="string", required=false), @Parameter(name="required", type="boolean", required=false)})
     */
    public function __construct($name = null, $type = "string", $required = false)
    {
        $this->name = $name;
        $this->type = $type;
        $this->required = $required;
    }

}

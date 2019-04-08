<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;

/**
 * @Annotation(on="\Weasel\Annotation\Config\Annotations\AnnotationCreator")
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
        $this->required = isset($required) ? $required : false;
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

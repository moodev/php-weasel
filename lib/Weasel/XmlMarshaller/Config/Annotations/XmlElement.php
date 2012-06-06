<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\AnnotationCreator;
use Weasel\Annotation\Config\Annotations\Parameter;

/**
 * @Annotation(on={"property", "method"})
 */
class XmlElement
{

    protected $name;
    protected $type;
    protected $namespace;
    protected $required;
    protected $nillable;

    /**
     * @param string $name
     * @param string $type
     * @param $nillable
     * @param string $namespace
     * @param bool $required
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false),
    @Parameter(name="type", type="string", required=false),
    @Parameter(name="nillable", type="bool", required=false),
    @Parameter(name="namespace", type="string", required=false),
    @Parameter(name="required", type="bool", required=false)})
     */
    public function __construct($name, $type, $nillable, $namespace, $required)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($type) ? $type : "string";
        $this->namespace = isset($namespace) ? $namespace : null;
        $this->required = isset($required) && $required;
        $this->nillable = isset($nillable) && $nillable;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getRequired()
    {
        return $this->required;
    }

    public function getNillable()
    {
        return $this->nillable;
    }

}


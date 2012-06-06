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
class XmlElementWrapper
{

    protected $name;
    protected $namespace;
    protected $required;
    protected $nillable;

    /**
     * @param string $name
     * @param $nillable
     * @param string $namespace
     * @param bool $required
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false),
    @Parameter(name="nillable", type="bool", required=false),
    @Parameter(name="namespace", type="string", required=false),
    @Parameter(name="required", type="bool", required=false)})
     */
    public function __construct($name, $nillable, $namespace, $required)
    {
        $this->name = isset($name) ? $name : null;
        $this->namespace = isset($namespace) ? $namespace : null;
        $this->required = isset($required) && $required;
        $this->nillable = isset($nillable) && $nillable;
    }

    public function getName()
    {
        return $this->name;
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


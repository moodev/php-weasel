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
class XmlAttribute
{

    protected $name;
    protected $type;
    protected $namespace;
    protected $required;

    /**
     * @param string $name
     * @param string $type
     * @param string $namespace
     * @param bool $required
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false),
    @Parameter(name="type", type="string", required=false),
    @Parameter(name="namespace", type="string", required=false),
    @Parameter(name="required", type="bool", required=false)})
     */
    public function __construct($name, $type, $namespace, $required)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($type) ? $type : "string";
        $this->namespace = isset($namespace) ? $namespace : null;
        $this->required = isset($required) && $required;
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

}


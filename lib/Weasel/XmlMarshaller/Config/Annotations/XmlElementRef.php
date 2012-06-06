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
 * @Annotation(on={"property", "method", "\Weasel\XmlMarshaller\Config\Annotations\XmlElementRefs"})
 */
class XmlElementRef
{

    protected $name;
    protected $type;
    protected $namespace;

    /**
     * @param string $name
     * @param string $type
     * @param string $namespace
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false),
    @Parameter(name="type", type="string", required=true),
    @Parameter(name="namespace", type="string", required=false)})
     */
    public function __construct($name, $type, $namespace)
    {
        $this->name = isset($name) ? $name : null;
        $this->type = isset($type) ? $type : null;
        $this->namespace = isset($namespace) ? $namespace : null;
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

}


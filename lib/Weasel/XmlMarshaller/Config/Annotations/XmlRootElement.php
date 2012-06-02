<?php
namespace Weasel\XmlMarshaller\Config\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

/**
 * @Annotation(on={"class"})
 */
class XmlRootElement
{

    protected $name;
    protected $namespace;

    /**
     * @param $name
     * @param string $namespace
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="namespace", type="string", required=false)})
     */
    public function __construct($name, $namespace)
    {
        $this->name = $name;
        $this->namespace = $namespace;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

}


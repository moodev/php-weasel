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
 * @Annotation(on={"property", "method", "class"})
 */
class XmlType
{

    /**
     * @var string
     */
    protected $factoryClass;

    /**
     * @var string
     */
    protected $factoryMethod;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $namespace;

    /**
     * @var string[]
     */
    protected $propOrder;

    /**
     * @param string $factoryClass
     * @param string $factoryMethod
     * @param string $name
     * @param string $namespace
     * @param string[] $propOrder
     * @AnnotationCreator({@Parameter(name="factoryClass", type="string", required=false),
    @Parameter(name="factoryMethod", type="string", required=false),
    @Parameter(name="name", type="string", required=false),
    @Parameter(name="namespace", type="string", required=false),
    @Parameter(name="propOrder", type="string[]", required=false)})
     */
    public function __construct($factoryClass, $factoryMethod, $name, $namespace, $propOrder)
    {
        $this->factoryClass = $factoryClass;
        $this->factoryMethod = $factoryMethod;
        $this->name = $name;
        $this->namespace = $namespace;
        $this->propOrder = empty($propOrder) ? null : $propOrder;
    }

    /**
     * @return string
     */
    public function getFactoryClass()
    {
        return $this->factoryClass;
    }

    /**
     * @return string
     */
    public function getFactoryMethod()
    {
        return $this->factoryMethod;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    public function getPropOrder()
    {
        return $this->propOrder;
    }

}


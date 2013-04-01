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
use Weasel\XmlMarshaller\Config\IAnnotations\IXmlRootElement;

/**
 * @Annotation(on={"class"})
 */
class XmlRootElement implements IXmlRootElement
{

    protected $name;
    protected $namespace;

    /**
     * @param $name
     * @param string $namespace
     * @AnnotationCreator({@Parameter(name="name", type="string", required=false), @Parameter(name="namespace", type="string", required=false)})
     */
    public function __construct($name = null, $namespace = null)
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


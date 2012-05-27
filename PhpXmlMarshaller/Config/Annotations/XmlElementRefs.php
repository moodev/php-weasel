<?php
namespace PhpXmlMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on={"property", "method"})
 */
class XmlElementRefs
{

    protected $values;
    protected $type;

    /**
     * @param \PhpXmlMarshaller\Config\Annotations\XmlElementRef[] $values
     * @param string $type
     * @AnnotationCreator({@Parameter(name="type", type="string", required=true), @Parameter(name="values", type="\PhpXmlMarshaller\Config\Annotations\XmlElementRef[]", required=true)})
     */
    public function __construct($type, array $values)
    {
        $this->values = $values;
        $this->type = $type;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function getType()
    {
        return $this->type;
    }

}


<?php
namespace PhpXmlMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on={"class"})
 */
class XmlDiscriminator
{

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value;
     * @AnnotationCreator({@Parameter(name="value", type="string", required=true)})
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }


}


<?php
namespace Weasel\XmlMarshaller\Config\Annotations;

use Weasel\Annotation\Annotations\Annotation;
use Weasel\Annotation\Annotations\AnnotationCreator;
use Weasel\Annotation\Annotations\Parameter;

/**
 * @Annotation(on={"property", "method"})
 */
class XmlElementRefs
{

    protected $values;
    protected $type;

    /**
     * @param \Weasel\XmlMarshaller\Config\Annotations\XmlElementRef[] $values
     * @param string $type
     * @AnnotationCreator({@Parameter(name="type", type="string", required=true), @Parameter(name="values", type="\XmlMarshaller\Config\Annotations\XmlElementRef[]", required=true)})
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


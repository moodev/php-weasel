<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on={"property", "method"})
 */
class JsonSerialize
{

    protected $as;

    /**
     * @param string $as
     * @AnnotationCreator(@Parameter(name="as", type="string", required=true))
     */
    public function __construct($as)
    {
        $this->as = $as;
    }

}

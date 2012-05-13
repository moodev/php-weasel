<?php
namespace PhpMarshaller\Config\Annotations;

use PhpAnnotation\Annotations\Annotation;
use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="class")
 */
class JsonIgnoreProperties
{

    /**
     * @param $names
     * @param $ignoreUnknown
     * @AnnotationCreator({@Parameter(name="names", type="string[]", required=false), @Parameter(name="ignoreUnknown", type="boolean", required=false)})
     */
    public function __construct($names, $ignoreUnknown)
    {
        $this->names = isset($names) ? $names : null;
        $this->ignoreUnknown = isset($ignoreUnknown) ? $ignoreUnknown : false;
    }

    protected $names;

    protected $ignoreUnknown;

}

<?php
namespace PhpAnnotation\Annotations;

use PhpAnnotation\Annotations\AnnotationCreator;
use PhpAnnotation\Annotations\Parameter;

/**
 * @Annotation(on="class", max=1)
 */
class Annotation
{

    protected $on;
    protected $max;

    /**
     * @param string[]|null $on
     * @param int|null $max
     * @AnnotationCreator({@Parameter(name="on", type="string[]", required=false), @Parameter(name="max", type="integer", required=false)})
     */
    public function __construct(array $on, $max) {
        $this->on = isset($on) ? $on : null;
        $this->max = isset($max) ? $max : null;
    }

    public function getMax()
    {
        return $this->max;
    }

    public function getOn()
    {
        return $this->on;
    }

}

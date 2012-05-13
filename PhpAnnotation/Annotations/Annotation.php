<?php
namespace PhpAnnotation\Annotations;

/**
 * @Annotation(on="class", max=1)
 */
class Annotation
{

    protected $on = null;
    protected $max = null;

    /**
     * @param string[]|null $on
     * @param int|null $max
     * @AnnotationCreator({@Parameter(name="on", type="string[]", required=false), @Parameter(name="max", type="integer", required=false)})
     */
    public function __construct(array $on = null, $max = null) {
        $this->on = $on;
        $this->max = null;
    }

}

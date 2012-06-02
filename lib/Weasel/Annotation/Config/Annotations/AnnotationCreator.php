<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation\Config\Annotations;

use Weasel\Annotation\Config\Annotations\Annotation;
use Weasel\Annotation\Config\Annotations\Parameter;

/**
 * @Annotation(on="method", max=1)
 */
class AnnotationCreator
{

    /**
     * @var array|null|\Weasel\Annotation\Config\Annotations\Parameter[]
     */
    protected $params;

    /**
     * @param \Weasel\Annotation\Config\Annotations\Parameter[]|null $params
     * @AnnotationCreator(@Parameter(name="params", type="\Weasel\Annotation\Config\Annotations\Parameter[]", required=false))
     */
    public function __construct(array $params)
    {
        $this->params = isset($params) ? $params : null;
    }

    /**
     * @return array|null|\Weasel\Annotation\Config\Annotations\Parameter[]
     */
    public function getParams()
    {
        return $this->params;
    }

}

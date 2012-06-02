<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation\Config;

class AnnotationConfig
{

    /**
     * @var \Weasel\Annotation\Config\Annotation[]
     */
    private $annotations = array();

    /**
     * @param \Weasel\Annotation\Config\Annotation $annotation
     */
    public function addAnnotation($annotation)
    {
        $this->annotations[$annotation->getClass()] = $annotation;
    }

    /**
     * @param \Weasel\Annotation\Config\Annotation[] $annotations
     * @return AnnotationConfig
     */
    public function setAnnotations($annotations)
    {
        $this->annotations = $annotations;
        return $this;
    }

    /**
     * @return Annotation[]
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    public function getAnnotation($class)
    {
        return isset($this->annotations[$class]) ? $this->annotations[$class] : null;
    }


}

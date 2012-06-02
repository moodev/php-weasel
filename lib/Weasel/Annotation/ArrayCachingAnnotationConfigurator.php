<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation;


class ArrayCachingAnnotationConfigurator extends AnnotationConfigurator
{

    protected $cache = array();

    public function get($name)
    {
        if (!isset($this->cache[$name])) {
            $this->cache[$name] = parent::get($name);
        }
        return $this->cache[$name];
    }


}


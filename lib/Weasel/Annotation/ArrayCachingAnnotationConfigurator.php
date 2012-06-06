<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
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


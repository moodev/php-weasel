<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

interface AnnotationConfigProvider
{

    /**
     * @abstract
     * @return \Weasel\Common\Logger\Logger
     */
    public function getLogger();

    /**
     * @abstract
     * @param string $name The name of the annotation to load
     * @return \Weasel\Annotation\Config\Annotation The annotation object
     */
    public function get($name);
}

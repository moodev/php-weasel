<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation;

interface AnnotationConfigProvider
{

    /**
     * @abstract
     * @return \Weasel\Logger\Logger
     */
    public function getLogger();

    /**
     * @abstract
     * @param string $name The name of the annotation to load
     * @return \Weasel\Annotation\Config\Annotation The annotation object
     */
    public function get($name);
}

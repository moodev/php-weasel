<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Weasel\Annotation\AnnotationReader;

/**
 * A config provider that uses Annotations
 * @deprecated
 */
class ArrayCachingAnnotationDriver extends AnnotationDriver
{

    public function __construct($logger = null, $annotationConfigurator = null) {
        parent::__construct($logger, $annotationConfigurator);
        $this->setCache(new \Weasel\Common\Cache\ArrayCache());
    }

}

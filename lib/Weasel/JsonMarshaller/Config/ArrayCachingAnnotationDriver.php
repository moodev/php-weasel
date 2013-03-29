<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config;

use Weasel\JsonMarshaller\Config\Annotations as Annotations;
use Psr\Log\LoggerInterface;
use Weasel\Common\Cache\ArrayCache;

/**
 * A config provider that uses Annotations
 * @deprecated Use the real driver, and pass in a cache implementation.
 */
class ArrayCachingAnnotationDriver extends AnnotationDriver
{

    public function __construct(LoggerInterface $logger = null, $annotationConfigurator = null)
    {
        parent::__construct($logger, $annotationConfigurator);
        $this->setCache(new ArrayCache());
    }

}

<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Weasel\Common\Cache\ArrayCache;

/**
 * Left over from prior to new cache system.
 * Stop using this. Use an AnnotationConfigurator, and call setCache.
 * @deprecated Use the AnnotationConfiguration and call setCache()!
 */
class ArrayCachingAnnotationConfigurator extends AnnotationConfigurator implements LoggerAwareInterface
{
    public function __construct(LoggerInterface $logger = null,
                                AnnotationReaderFactory $readerFactory = null)
    {
        parent::__construct($logger, $readerFactory);
        $this->setCache(new ArrayCache("AnnotationConfigurator"));
    }


}


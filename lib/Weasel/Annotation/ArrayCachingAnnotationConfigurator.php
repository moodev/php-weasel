<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;


use Weasel\Common\Cache\ArrayCache;

/**
 * Left over from prior to new cache system.
 * Stop using this. Use an AnnotationConfigurator, and call setCache.
 * @deprecated
 */
class ArrayCachingAnnotationConfigurator extends AnnotationConfigurator
{
    public function __construct(\Weasel\Common\Logger\Logger $logger = null,
                                AnnotationReaderFactory $readerFactory = null)
    {
        parent::__construct($logger, $readerFactory);
        $this->setCache(new ArrayCache("AnnotationConfigurator"));
    }


}


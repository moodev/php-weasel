<?php
namespace Weasel\Common\Cache;

interface CacheAwareInterface
{

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache);

}

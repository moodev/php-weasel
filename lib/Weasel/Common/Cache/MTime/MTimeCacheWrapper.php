<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache\MTime;

use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\Exception\NotFound;
use Weasel\Common\Cache\Exception as Exception;

class MTimeCacheWrapper extends Cache
{

    /**
     * @var Cache
     */
    protected $cache;

    function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * @param string $key Key to load from cache.
     * @param integer $ifModifiedSince
     * @throws \Weasel\Common\Cache\Exception\BackingFailure
     * @throws \Weasel\Common\Cache\Exception\NotFound
     * @return mixed
     */
    public function get($key, $ifModifiedSince = null)
    {
        $wrapped = $this->cache->get($key);
        if (!$wrapped instanceof MTimeStorable) {
            throw new Exception\BackingFailure($key, "Retrieved value is not instance of MTimeStorable, got: " .
                gettype($wrapped));
        }
        /**
         * @var MTimeStorable $wrapped
         */
        if ($wrapped->getMtime() === null || !isset($notModifiedSince) || $wrapped->getMtime() >= $ifModifiedSince) {
            return $wrapped->getValue();
        }
        throw new NotFound($key);
    }

    /**
     * @param string $key Key to store data under
     * @param mixed $value Data to store. If it's serializable it can be stored.
     * @param null $mtime
     * @return void
     */
    public function set($key, $value, $mtime = null)
    {
        $wrapped = new MTimeCacheWrapper($mtime, $value);
        $this->cache->set($key, $wrapped);
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->cache->setPrefix($prefix);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->cache->getPrefix();
    }
}

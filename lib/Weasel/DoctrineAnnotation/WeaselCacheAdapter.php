<?php
namespace Weasel\DoctrineAnnotation;

use Doctrine\Common\Cache\CacheProvider;
use Weasel\Common\Cache\Cache;
use Weasel\Common\Cache\CacheAwareInterface;

/**
 * Adapter to allow a Weasel Cache to be used as a Doctrine cache.
 */
class WeaselCacheAdapter extends CacheProvider implements CacheAwareInterface
{

    /**
     * @var Cache
     */
    protected $delegate;

    public function __construct(Cache $cache)
    {
        $this->delegate = $cache;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return string|bool The cached data or FALSE, if no cache entry exists for the given id.
     */
    protected function doFetch($id)
    {
        $found = false;
        $val = $this->delegate->get($id, null, $found);
        if ($found) {
            return $val;
        } else {
            return false;
        }
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return boolean TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        $found = false;
        $this->delegate->get($id, null, $found);
        return $found;
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return boolean TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        $this->delegate->set($id, $data);
        return true;
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doDelete($id)
    {
        $this->delegate->delete($id);
        return true;
    }

    /**
     * Flushes all cache entries.
     *
     * @return boolean TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doFlush()
    {
        return false;
    }

    /**
     * Retrieves cached information from the data store.
     *
     * @since 2.2
     *
     * @return array|null An associative array with server's statistics if available, NULL otherwise.
     */
    protected function doGetStats()
    {
        return null;
    }

    /**
     * @param Cache $cache
     */
    public function setCache(Cache $cache)
    {
        $this->delegate = $cache;
    }
}

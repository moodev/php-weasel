<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache;

class ArrayCache extends Cache
{

    private $cache = array();

    /**
     * @param string $key Key to load from cache.
     * @param null|string $namespace
     * @throws Exception\NotFound
     * @return mixed
     */
    public function get($key, $namespace = null)
    {
        $key = $this->_getRealKeyName($key, $namespace);
        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }
        throw new Exception\NotFound($key);
    }

    /**
     * @param string $key Key to store data under
     * @param mixed $value Data to store. If it's serializable it can be stored.
     * @param null $namespace
     * @return void
     */
    public function set($key, $value, $namespace = null)
    {
        $key = $this->_getRealKeyName($key, $namespace);
        $this->cache[$key] = $value;
    }

}

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
     * @param bool $found
     * @return mixed
     */
    public function get($key, $namespace = null, &$found = true)
    {
        $key = $this->_getRealKeyName($key, $namespace);
        if (array_key_exists($key, $this->cache)) {
            $found = true;
            return $this->cache[$key];
        }
        $found = false;
        return null;
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

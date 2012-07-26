<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache;

class ApcCache extends Cache
{

    /**
     * @param string $key Key to load from cache.
     * @param null $namespace
     * @param bool $found
     * @return mixed
     */
    public function get($key, $namespace = null, &$found = true)
    {
        $key = $this->_getRealKeyName($key, $namespace);
        $success = false;
        $res = apc_fetch($key, $success);
        if (!$success) {
            $found = false;
            return null;
        }
        $found = true;
        return $res;
    }

    /**
     * @param string $key Key to store data under
     * @param mixed $value Data to store. If it's serializable it can be stored.
     * @param null $namespace
     * @throws Exception\BackingFailure
     * @return void
     */
    public function set($key, $value, $namespace = null)
    {
        $key = $this->_getRealKeyName($key, $namespace);
        if (!apc_store($key, $value)) {
            throw new Exception\BackingFailure($key, "apc_store returned false");
        }
    }

}

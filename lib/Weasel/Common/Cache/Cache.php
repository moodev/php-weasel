<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache;

abstract class Cache
{

    private $prefix;

    /**
     * @abstract
     * @param string $key Key to load from cache.
     * @param string $namespace
     * @param bool $found True if the key was found in the cache, false if it wasn't. Useful if the cache can store nulls.
     * @return mixed
     */
    public abstract function get($key, $namespace = null, &$found = true);

    /**
     * @abstract
     * @param string $key Key to store data under
     * @param mixed $value Data to store. If it's serializable it can be stored.
     * @param string $namespace
     * @return
     */
    public abstract function set($key, $value, $namespace = null);

    /**
     * Delete something from the cache.
     * To preserve keep old implementations working this base class implements the method to lob an error.
     * This MUST be implemented for the DoctrineAnnotation cache adapter to work.
     * @param string $key Key to delete
     * @param string $namespace
     * @throws \RuntimeException
     */
    public function delete($key, $namespace = null)
    {
        throw new \RuntimeException("Unsupported for this cache implementation");
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    public function __construct($prefix = null)
    {
        $this->setPrefix($prefix);
    }

    protected function _getRealKeyName($key, $namespace = null)
    {
        if (isset($namespace)) {
            $key = $namespace . ':~:' . $key;
        }
        if (isset($this->prefix)) {
            $key = $this->prefix . '::' . $key;
        }
        return $key;
    }

}

<?php
namespace Weasel\Common\Utils;

/**
 * Anything that extends this will throw exceptions if you try to access an invalid property.
 * It'll also throw exceptions if you unset a property, since doing that is evil.
 */
class NoUndeclaredProperties
{

    public function __get($name)
    {
        throw new \InvalidArgumentException("No such property $name");
    }

    public function __set($name, $value)
    {
        throw new \InvalidArgumentException("No such property $name");
    }

    public function __isset($name)
    {
        throw new \InvalidArgumentException("No such property $name");
    }

    public function __unset($name)
    {
        throw new \InvalidArgumentException("You cannot unset properties.");
    }

}

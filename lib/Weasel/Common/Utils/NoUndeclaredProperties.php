<?php
namespace Weasel\Common\Utils;

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

}

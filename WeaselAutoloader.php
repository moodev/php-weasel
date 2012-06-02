<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel;
    /**
     * @package MooPhp
     * @author Jonathan Oddy <jonathan at woaf.net>
     * @copyright Copyright (c) 2011, Jonathan Oddy
     */

/**
 * @param string $name Class to load
 * @return void
 */
function autoLoad($name)
{
    if (class_exists($name) || interface_exists($name)) {
        return;
    }
    $exploded = explode("\\", $name);
    $vendor = $exploded[0];
    if ($vendor != "Weasel") {
        return;
    }
    $path = implode('/', $exploded);
    /** @noinspection PhpIncludeInspection */
    include(__DIR__ . '/lib/' . $path . '.php');
    return;
}

spl_autoload_register('\Weasel\autoLoad');


<?php
namespace PhpMarshaller;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

/**
 * @param string $name Class to load
 * @return void
 */
function autoLoad($name) {
    if (class_exists($name) || interface_exists($name)) {
        return;
    }
    $exploded = explode("\\", $name);
    $vendor = $exploded[0];
    if ($vendor != "PhpMarshaller" && $vendor != "PhpAnnotation") {
        return;
    }
    $path = implode('/', $exploded);
    include(__DIR__ . '/' . $path . '.php');
    return;
}

spl_autoload_register('\PhpMarshaller\autoLoad');


<?php
namespace PhpAnnotation;

/**
 * @param string $name Class to load
 * @return void
 */
function autoLoad($name) {
	if (class_exists($name) || interface_exists($name)) {
		return;
	}
	$exploded = explode("\\", $name);
	if (array_shift($exploded) != "PhpAnnotation") {
		return;
	}
	$path = implode('/', $exploded);
	include(dirname(__FILE__) . '/' . $path . '.php');
	return;
}

spl_autoload_register('\PhpAnnotation\autoLoad');

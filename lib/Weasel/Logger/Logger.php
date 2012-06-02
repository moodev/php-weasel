<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Logger;

interface Logger
{

    const LOG_LEVEL_OFF = 0;
    const LOG_LEVEL_DEBUG = 255;

    public function setLogLevel($level);

    public function logDebug($entry);

}

<?php
namespace Weasel\Logger;
/**
 * @package MooPhp
 * @author Jonathan Oddy <jonathan at woaf.net>
 * @copyright Copyright (c) 2011, Jonathan Oddy
 */

interface Logger
{

    const LOG_LEVEL_OFF = 0;
    const LOG_LEVEL_DEBUG = 255;

    public function setLogLevel($level);

    public function logDebug($entry);

}

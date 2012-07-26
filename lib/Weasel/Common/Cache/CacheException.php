<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache;

use Exception;

class CacheException extends Exception
{

    protected $key;

    public function __construct($key, $message = "", $code = 0, Exception $previous = null)
    {
        $this->key = $key;
        parent::__construct($message, $code, $previous);
    }

}

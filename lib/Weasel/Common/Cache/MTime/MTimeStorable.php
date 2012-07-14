<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Common\Cache\MTime;

class MTimeStorable
{

    protected $value;
    protected $mtime;

    public function __construct($mtime, $value)
    {
        $this->mtime = $mtime;
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function getMtime()
    {
        return $this->mtime;
    }


}

<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Exception;
use Exception;

class InvalidTypeException extends JsonMarshallerException
{

    protected $expectedType;
    protected $gotType;
    protected $gotValue;

    public function __construct($expectedType, $gotValue, $code = 0, Exception $previous = null)
    {
        $this->expectedType = $expectedType;
        $this->gotType = gettype($gotValue);
        $this->gotValue = $gotValue;
        $message = "Expected $expectedType but got ({$this->gotType})$gotValue";
        parent::__construct($message, $code, $previous);
    }


}

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

    protected function _stringifyValue($value)
    {
        $typeStr = '(' . gettype($value) . ')';
        if (is_int($value) || is_float($value) || (is_object($value) && method_exists($value, "__toString"))) {
            return $typeStr . $value;
        } elseif (is_string($value)) {
            return $typeStr . '"' . addcslashes($value, '"') . '"';
        } elseif (is_bool($value)) {
            return $typeStr . ($value ? "true" : "false");
        } elseif (is_array($value)) {
            $pos = current($value);
            $retval = $typeStr . '{';
            $fst = true;
            foreach ($value as $key => $element) {
                if (!$fst) {
                    $retval .= ', ';
                }
                $retval .= $this->_stringifyValue($key) . ' => ' . $this->_stringifyValue($element);
                $fst = false;
            }
            $retval .= '}';

            if ($pos !== false) {
                for (reset($value); current($value) != $pos && current($value) !== false; each($value)) {
                    ;
                }
            }

            return $retval;
        }
        return $typeStr;
    }

    /**
     * @param string $expected
     * @param mixed $gotValue
     * @param int $code
     * @param \Exception $previous
     */
    public function __construct($expected, $gotValue, $code = 0, Exception $previous = null)
    {
        $this->expectedType = $expected;
        $this->gotType = gettype($gotValue);
        $this->gotValue = $gotValue;
        $message = "Expected $expected but got " . $this->_stringifyValue($gotValue);
        parent::__construct($message, $code, $previous);
    }


}

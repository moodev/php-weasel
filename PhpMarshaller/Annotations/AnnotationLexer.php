<?php
namespace PhpMarshaller\Annotations;
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12/05/12
 * Time: 18:48
 * To change this template use File | Settings | File Templates.
 */
class AnnotationLexer
{

    const T_MEH = 1;
    const T_NULL = 10;
    const T_QUOTED_STRING = 11;
    const T_INTEGER = 12;
    const T_FLOAT = 13;
    const T_BOOLEAN = 14;

    const T_IDENTIFIER = 50;

    const T_AT = 60;
    const T_OPEN_PAREN = 61;
    const T_CLOSE_PAREN = 62;
    const T_OPEN_BRACE = 63;
    const T_CLOSE_BRACE = 64;
    const T_COMMA = 65;
    const T_EQUAL = 66;
    const T_BACKSLASH = 67;
    const T_COLON = 68;


    protected $lexed = array();

    /**
     * @param string $input
     * @throws \Exception
     */
    public function scan($input) {

        $pos = 0;
        $captured = null;
        $len = strlen($input);

        while ($pos < $len) {
            $cur = $input[$pos];
            $type = self::T_MEH;
            $value = null;
            switch ($cur) {
                case '@':
                    $type = self::T_AT;
                    break;
                case '(':
                    $type = self::T_OPEN_PAREN;
                    break;
                case ')':
                    $type = self::T_CLOSE_PAREN;
                    break;
                case '{':
                    $type = self::T_OPEN_BRACE;
                    break;
                case '}':
                    $type = self::T_CLOSE_BRACE;
                    break;
                case ',':
                    $type = self::T_COMMA;
                    break;
                case '=':
                    $type = self::T_EQUAL;
                    break;
                case '\\':
                    $type = self::T_BACKSLASH;
                    break;
                case ':':
                    $type = self::T_COLON;
                    break;
                case '"':
                    $type = self::T_QUOTED_STRING;
                    if ($pos >= $len) {
                        throw new \Exception("Unterminated quoted string");
                    }
                    $cur = $input[++$pos];
                    $value = "";
                    while ($cur !== '"') {
                        $value .= $cur;
                        if ($cur === '\\') {
                            $pos++;
                            if ($pos <= $len) {
                                // Capture the escaped char too
                                $value .= $input[$pos];
                            }
                        }
                        $pos++;
                        if ($pos > $len) {
                            throw new \Exception("Unterminated quoted string");
                        }
                        $cur = $input[$pos];
                    }
                    break;
                default:
                    $matches = array();
                    if (preg_match(array('/^[+-]?[0-9]+(?:\.[0-9]+|[eE][0-9]+)/'), $input, $matches, 0, $pos)) {
                        $match = $matches[0];

                    }
            }
            $pos++;
        }


    }

}

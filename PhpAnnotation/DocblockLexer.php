<?php
namespace PhpAnnotation;
/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12/05/12
 * Time: 18:48
 * To change this template use File | Settings | File Templates.
 */
class DocblockLexer
{

    const T_MEH = 1;
    const T_WHITESPACE = 2;
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
    const T_DOT = 69;

    protected $tokens = array();

    protected $cur = null;

    public function __construct($input) {
        $this->_scan($input);
    }


    /**
     * @param string $input
     * @throws \Exception
     */
    protected function _scan($input) {

        // In a more traditional world we'd scan the input a single char at a time
        // Fortunately preg_split gives us a way to split the input up into a slightly more helpful form.
        // Yay.
        $split = preg_split(
            '(' . implode('|', array(
                '("(?:[^"]|"")*")', // Quoted strings
                '([+-]?[0-9]+(?:\.[0-9]+|[eE][+-]?[0-9]+)?)', // Numeric
                '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)', // Identifier
                '(\s+)', // Whitespace
                '(.)', // Everything else will be split into single chars
                )) . ')',
            $input,
            -1,
            (PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE | PREG_SPLIT_DELIM_CAPTURE)
        );

        foreach ($split as $segment) {
            list ($token, $position) = $segment;
            $type = $this->_processToken($token);
            $tokens['token'] = $token;
            $tokens['type'] = $type;
            $tokens['pos'] = $position;
            $this->tokens[] = $tokens;
        }
        $cur = reset($this->tokens);
        if ($cur === false) {
            $this->cur = null;
        } else {
            $this->cur = $cur;
        }

    }

    protected function _processToken(&$value) {

        switch (strtolower($value)) {
            case '@':
                return self::T_AT;
                break;
            case '(':
                return self::T_OPEN_PAREN;
                break;
            case ')':
                return self::T_CLOSE_PAREN;
                break;
            case '{':
                return self::T_OPEN_BRACE;
                break;
            case '}':
                return self::T_CLOSE_BRACE;
                break;
            case ',':
                return self::T_COMMA;
                break;
            case '=':
                return self::T_EQUAL;
                break;
            case '\\':
                return self::T_BACKSLASH;
                break;
            case ':':
                return self::T_COLON;
                break;
            case '.':
                return self::T_DOT;
                break;
            case 'true':
                $value = true;
                return self::T_BOOLEAN;
            case 'false':
                $value = false;
                return self::T_BOOLEAN;
            case 'null':
                return self::T_NULL;
        }

        if ($value[0] === '"' && strlen($value) > 1) {
            $value = str_replace('""', '"', substr($value, 1, -1));
            return self::T_QUOTED_STRING;
        }

        if (is_numeric($value)) {
            if (strpos($value, '.') !== false || stripos($value, 'e') !== false) {
                return self::T_FLOAT;
            }
            return self::T_INTEGER;
        }

        if (preg_match('/[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)/', $value) > 1) {
            return self::T_IDENTIFIER;
        }

        if (preg_match('/\s+/', $value) > 1) {
            return self::T_WHITESPACE;
        }

        return self::T_MEH;
    }

    public function seekToType($target) {
        while ($cur = $this->read()) {
            if (is_array($cur) && $cur["type"] === $target) {
                return $cur;
            }
        }
        return null;
    }

    public function skip($type = self::T_WHITESPACE) {
        while ($this->peek() === $type) {
            $this->read();
        }
    }

    public function read($skipWS = false) {
        $cur = $this->cur;
        $this->cur = next($this->tokens);
        if ($this->cur === false) {
            $this->cur = null;
        }
        return $cur;
    }

    public function peek($num = 1) {
        if (!isset($this->cur)) {
            return null;
        }
        if ($num === 1) {
            return $this->cur['type'];
        }

        $ret = next($this->tokens);
        prev($this->tokens);
        if ($ret === false) {
            return null;
        }
        return $ret['type'];
    }

    public function readAndCheck($type) {
        $cur = $this->read();
        if (!is_array($cur)) {
            throw new \Exception("Parse error got $cur expected $type");
        }
        if ($cur['type'] !== $type) {
            throw new \Exception("Parse error got {$cur['type']} (\"{$cur['token']}\") expected $type");
        }
        return $cur;
    }

}

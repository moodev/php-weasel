<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;
class DocblockLexer
{

    const T_MEH = 1;
    const T_WHITESPACE = 2;
    const T_EOL = 3;
    const T_PREAMBLE = 4;
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

    public static $TREAT_AS_WS = array(self::T_WHITESPACE,
                                       self::T_EOL,
                                       self::T_PREAMBLE
    );

    protected $tokens = array();

    protected $pos;

    public function __construct($input)
    {
        $this->_scan($input);
    }


    /**
     * @param string $input
     * @throws \Exception
     */
    protected function _scan($input)
    {

        // In a more traditional world we'd scan the input a single char at a time
        // Fortunately preg_split gives us a way to split the input up into a slightly more helpful form.
        // Yay.
        $split = preg_split(
            '(' . implode('|', array(
                                    // Quoted strings
                                    '("(?:[^"]|"")*")',
                                    // Numeric
                                    '([+-]?[0-9]+(?:\.[0-9]+|[eE][+-]?[0-9]+)?)',
                                    // Identifier
                                    '([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)',
                                    // Line preamble
                                    '(\v+\s+\*[^\S\v])',
                                    // End of line
                                    '(\v+)',
                                    // All other whitespace
                                    '(\s+)',
                                    // Everything else will be split into single chars
                                    '(.)',
                               )
            ) . ')',
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
        $this->pos = 0;
    }

    protected function _processToken(&$value)
    {

        switch (strtolower($value)) {
            case '@':
                return self::T_AT;
            case '(':
                return self::T_OPEN_PAREN;
            case ')':
                return self::T_CLOSE_PAREN;
            case '{':
                return self::T_OPEN_BRACE;
            case '}':
                return self::T_CLOSE_BRACE;
            case ',':
                return self::T_COMMA;
            case '=':
                return self::T_EQUAL;
            case '\\':
                return self::T_BACKSLASH;
            case ':':
                return self::T_COLON;
            case '.':
                return self::T_DOT;
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

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $value)) {
            return self::T_IDENTIFIER;
        }

        if (preg_match('/^(\v+\s+\*\s+)$/', $value)) {
            return self::T_PREAMBLE;
        }

        if (preg_match('/^\v+$/', $value)) {
            return self::T_EOL;
        }

        if (preg_match('/^\s+$/', $value)) {
            return self::T_WHITESPACE;
        }

        return self::T_MEH;
    }

    public function skipToType($target)
    {
        if (!$cur = $this->get()) {
            return null;
        }
        do {
            if ($cur['type'] === $target) {
                return $cur;
            }
        } while ($cur = $this->next());
        return null;
    }

    public function get()
    {
        if (!isset($this->tokens[$this->pos])) {
            return null;
        } else {
            return $this->tokens[$this->pos];
        }
    }

    public function skip($types = null)
    {
        if (!isset($types)) {
            $types = self::$TREAT_AS_WS;
        }
        if (!$cur = $this->get()) {
            return null;
        }
        do {
            if (!in_array($cur['type'], $types)) {
                return $cur;
            }
        } while ($cur = $this->next());
        return null;
    }

    public function next($skipWS = false)
    {
        $this->pos++;
        if ($skipWS) {
            return $this->skip();
        }
        return $this->get();
    }

    public function seek($to = 0)
    {
        if ($to < 0) {
            $to = count($this->tokens) + $to;
        }
        if (!isset($this->tokens[$to])) {
            return null;
        }
        $this->pos = $to;
        return $this->get();
    }

    public function cur()
    {
        return $this->pos;
    }

    protected function _wsSkippingPeek($num = 1)
    {
        if ($num === 0) {
            $cur = $this->get();
            if (!in_array($cur["type"], self::$TREAT_AS_WS)) {
                return $cur["type"];
            }
        }
        $pos = $this->pos;
        $ret = null;
        $i = 0;
        do {
            $ret = $this->next(true);
            $i++;
        } while ($i < $num && $ret !== null);
        $this->seek($pos);
        return isset($ret) ? $ret["type"] : null;
    }

    public function peek($num = 1, $skipWS = false)
    {
        if ($num < 0) {
            throw new \InvalidArgumentException("Cannot peek backwards");
        }
        if ($skipWS) {
            return $this->_wsSkippingPeek($num);
        }
        if ($num === 0) {
            $cur = $this->get();
            return $cur["type"];
        }
        $pos = $this->pos;
        if ($ret = $this->seek($pos + $num)) {
            $this->seek($pos);
            return $ret['type'];
        }
        $this->seek($pos);
        return null;
    }

}

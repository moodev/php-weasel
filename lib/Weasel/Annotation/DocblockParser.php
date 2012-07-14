<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

class DocblockParser
{

    /**
     * @var AnnotationConfigProvider
     */
    protected $annotations;

    /**
     * @var \Weasel\Common\Logger\Logger
     */
    protected $logger;

    static $silentlyDiscard = array(
        "param",
        "author",
        "copyright",
        "var",
        "return",
        "package",
        "throws"
    );

    public function parse($docBlock, $location, $namespaces)
    {
        return $this->_parse($docBlock, $location, $namespaces);
    }

    public function __construct(AnnotationConfigProvider $annotations)
    {
        $this->annotations = $annotations;
        $this->logger = $annotations->getLogger();
    }

    protected function _getAnnotation($name, $namespaces)
    {

        if ($name[0] !== '\\') {
            $exploded = explode('\\', $name, 2);

            if (!isset($namespaces[$exploded[0]])) {
                return null;
            }
            $namespace = $namespaces[$exploded[0]];

            $name = '\\' . $namespace . (isset($exploded[1]) ? '\\' . $exploded[1] : '');
        }

        return $this->annotations->get($name);
    }

    protected function _parse($input, $location, $namespaces)
    {
        $lexer = new DocblockLexer($input);
        return $this->_DocBlock($lexer, $location, $namespaces);
    }

    protected function _DocBlock(DocblockLexer $lexer, $location, $namespaces)
    {
        $annotations = array();
        while ($lexer->skipToType(DocblockLexer::T_PREAMBLE)) {
            $next = $lexer->next();
            if ($next["type"] !== DocblockLexer::T_AT) {
                // Skip because it doesn't have an annotation at the start
                continue;
            }
            $pos = $lexer->cur();
            try {
                $annotation = $this->_Annotation($lexer, $location, $namespaces);
                $this->_expectNext($lexer, DocblockLexer::$TREAT_AS_WS);
                if (isset($annotation)) {
                    $annotations[$annotation[0]][] = $annotation[1];
                } else {
                    // If not then it parsed fine, but it isn't something we know about
                }
            } catch (\Exception $e) {
                // OK, try starting 1 char after the @ to find the next annotation.
                if ($this->logger) {
                    $this->logger->logDebug("Skipping syntax error: " . $e->getMessage());
                }
                $lexer->seek($pos);
                if (!$lexer->next()) {
                    break;
                }
            }
        }
        return $annotations;
    }

    protected function _Array(DocblockLexer $lexer, $location, $namespaces)
    {

        $elements = array();

        while (($next = $lexer->peek(1, true)) !== DocblockLexer::T_CLOSE_BRACE) {
            $elements[] = $this->_ParamValue($lexer, $location, $namespaces);

            if ($lexer->peek(1, true) === DocblockLexer::T_CLOSE_BRACE) {
                break;
            }
            $this->_expectNext($lexer, DocblockLexer::T_COMMA, true);
        }
        $this->_expectNext($lexer, DocblockLexer::T_CLOSE_BRACE, true);
        return $elements;
    }

    protected function _ParamValue(DocblockLexer $lexer, $location, $namespaces)
    {
        $next = $lexer->peek(1, true);
        if ($next === DocblockLexer::T_IDENTIFIER) {
            // Might be an enum then...
            $enum = $this->_Enum($lexer, $location, $namespaces);
            return $enum;
        }

        $cur = $lexer->next(true);
        switch ($cur['type']) {
            case DocblockLexer::T_INTEGER:
                $param = array('integer',
                               $cur['token']
                );
                break;
            case DocblockLexer::T_FLOAT:
                $param = array('float',
                               $cur['token']
                );
                break;
            case DocblockLexer::T_BOOLEAN:
                $param = array('boolean',
                               $cur['token']
                );
                break;
            case DocblockLexer::T_QUOTED_STRING:
                $param = array('string',
                               $cur['token']
                );
                break;
            case DocblockLexer::T_AT:
                $object = $this->_Annotation($lexer, $location, $namespaces);
                return $object;
                break;
            case DocblockLexer::T_OPEN_BRACE:
                $array = $this->_Array($lexer, $location, $namespaces);
                return array('array',
                             $array
                );
            default:
                throw new \Exception("Parse error got {$cur["type"]} ({$cur['token']})");
        }
        return $param;
    }

    protected function _NamedParam(DocblockLexer $lexer, $location, $namespaces)
    {
        $nameToken = $this->_expectNext($lexer, DocblockLexer::T_IDENTIFIER, true);

        $this->_expectNext($lexer, DocblockLexer::T_EQUAL, true);

        $value = $this->_ParamValue($lexer, $location, $namespaces);

        return array($nameToken['token'],
                     $value
        );

    }

    protected function _expectNext(DocblockLexer $lexer, $types, $skipWS = false)
    {
        if (!is_array($types)) {
            $types = array($types);
        }
        $next = $lexer->next($skipWS);
        if (!$next || !in_array($next['type'], $types, true)) {
            throw new \Exception('Parse error, expected one of ' . implode(',', $types) . ' but got ' . ($next ?
                $next['type'] : 'EOF'));
        }
        return $next;
    }

    protected function _ClassName(DocblockLexer $lexer)
    {
        $next = $lexer->next();

        $class = '';
        if ($next['type'] === DocblockLexer::T_BACKSLASH) {
            $class .= '\\';
            $next = $this->_expectNext($lexer, DocblockLexer::T_IDENTIFIER);
        }

        $class .= $next['token'];

        while ($lexer->peek() === DocblockLexer::T_BACKSLASH) {
            $this->_expectNext($lexer, DocblockLexer::T_BACKSLASH);
            $class .= '\\';
            $part = $this->_expectNext($lexer, DocblockLexer::T_IDENTIFIER);
            $class .= $part['token'];
        }
        return $class;
    }

    protected function _Enum(DocblockLexer $lexer, $location, $namespaces)
    {
        $class = $this->_ClassName($lexer);
        $meta = $this->_getAnnotation($class, $namespaces);
        if (!$meta) {
            throw new \Exception("Unable to resolve enum class $class");
        }

        $this->_expectNext($lexer, DocblockLexer::T_DOT);
        $enumTok = $this->_expectNext($lexer, DocblockLexer::T_IDENTIFIER);
        $enum = $enumTok["token"];
        $this->_expectNext($lexer, DocblockLexer::T_DOT);
        $indexTok = $this->_expectNext($lexer, DocblockLexer::T_IDENTIFIER);
        $index = $indexTok["token"];

        if ($meta->getEnum($enum) === null) {
            throw new \Exception("Unable to find an enum for $class : $enum : $index");
        }
        $enumValues = $meta->getEnum($enum)->getValues();
        if (!isset($enumValues[$index])) {
            throw new \Exception("Unable to lookup enum value for $class : $enum : $index");
        }

        return array("integer",
                     $enumValues[$index]
        );

    }

    protected function _Annotation(DocblockLexer $lexer, $location, $namespaces)
    {

        $identifier = $this->_ClassName($lexer);

        $meta = $this->_getAnnotation($identifier, $namespaces);
        if (!$meta) {
            if ($this->logger) {
                if (!in_array($identifier, self::$silentlyDiscard)) {
                    $this->logger->logDebug("Skipping unknown annotation: $identifier");
                }
            }
            return null;
        }

        if ($meta->getOn() && !in_array($location, $meta->getOn())) {
            throw new \Exception(
                "Found annotation in wrong location, got $location but expected one of " . implode(", ",
                                                                                                   $meta->getOn(
                                                                                                   )
                ));
        }

        if ($lexer->peek() === DocblockLexer::T_OPEN_PAREN) {
            // There are params to read
            $this->_expectNext($lexer, array(DocblockLexer::T_OPEN_PAREN));

            $anonParams = array();
            $namedParams = array();

            $expectingComma = false;
            while (($next = $lexer->peek(1, true)) !== DocblockLexer::T_CLOSE_PAREN) {
                if ($next === null) {
                    throw new \Exception('Unmatched parentheses');
                }
                if ($next === DocblockLexer::T_IDENTIFIER && $lexer->peek(2, true) === DocblockLexer::T_EQUAL) {
                    if ($expectingComma) {
                        throw new \Exception('Unexpected identifier, expecting comma or close paren');
                    }
                    list($name, $param) = $this->_NamedParam($lexer, $meta->getClass(), $namespaces);
                    $namedParams[$name] = $param;
                    $expectingComma = true;
                } elseif ($next === DocblockLexer::T_COMMA) {
                    if (!$expectingComma) {
                        throw new \Exception('Unexpected comma');
                    }
                    $this->_expectNext($lexer, DocblockLexer::T_COMMA, true);
                    $expectingComma = false;
                } else {
                    if ($expectingComma) {
                        throw new \Exception('Unexpected value, expecting comma or close paren');
                    }
                    $anonParams[] = $this->_ParamValue($lexer, $meta->getClass(), $namespaces);
                    $expectingComma = true;
                }
            }
            if (!empty($anonParams) && !empty($namedParams)) {
                throw new \Exception('Named or anonymous params, pick one.');
            }

            $this->_expectNext($lexer, DocblockLexer::T_CLOSE_PAREN, true);

            if ((!empty($anonParams) || !empty($namedParams)) && $expectingComma === false) {
                // There has been a comma followed by a close paren...
                throw new \Exception('Unexpected close paren after comma');
            }

        }

        $class = $meta->getClass();

        if ($meta->getCreatorMethod()) {
            // There's a creator method to call

            /**
             * @var \Weasel\Annotation\Config\Param[] $expectedParams
             */
            $expectedParams = $meta->getCreatorParams() ? $meta->getCreatorParams() : array();
            $actualParams = array();
            if (!empty($anonParams)) {
                if (count($anonParams) > count($expectedParams)) {
                    throw new \Exception("Too many parameters");
                }
                reset($anonParams);
                foreach ($expectedParams as $paramConfig) {
                    $param = each($anonParams);
                    $param = ($param === false) ? null : $param['value'];
                    if ($param === null) {
                        if ($paramConfig->getRequired() && $paramConfig->getRequired() === true) {
                            throw new \Exception('Missing required parameter ' . $paramConfig->getName());
                        }
                        $actualParams[] = null;
                    } else {
                        $actualParams[] = $this->_collapseAndCheckType($param, $paramConfig->getType());
                    }
                }
            } elseif (!empty($namedParams)) {
                foreach ($expectedParams as $paramConfig) {
                    if (!isset($namedParams[$paramConfig->getName()])) {
                        if ($paramConfig->getRequired() && $paramConfig->getRequired() === true) {
                            throw new \Exception('Missing required parameter ' . $paramConfig->getName());
                        }
                        $actualParams[] = null;
                    } else {
                        $actualParams[] = $this->_collapseAndCheckType($namedParams[$paramConfig->getName()],
                                                                       $paramConfig->getType()
                        );
                    }
                }
            } else {
                $actualParams = array_fill(0, count($expectedParams), null);
            }

            if ($meta->getCreatorMethod() === '__construct') {
                $reflectionClass = new \ReflectionClass($class);
                $annotation = $reflectionClass->newInstanceArgs($actualParams);
            } else {
                $method = $meta->getCreatorMethod();
                $reflectionMethod = new \ReflectionMethod($class, $method);
                $annotation = $reflectionMethod->invokeArgs(null, $actualParams);
            }
        } else {
            $annotation = new $class();
        }

        if (!empty($namedParams) && $meta->getProperties()) {
            foreach ($meta->getProperties() as $name => $property) {
                /**
                 * @var Config\Property $property
                 */
                if (isset($namedParams[$name])) {
                    $annotation->$name = $this->_collapseAndCheckType($namedParams[$name], $property->getType());
                }
            }
        }

        return array($class,
                     $annotation
        );
    }

    protected function _collapseAndCheckType($param, $type)
    {
        list($paramType, $paramValue) = $param;
        $matches = array();
        if (!preg_match('/^(.*)\\[(int|integer|string|bool|boolean|float|)\\]$/i', $type, $matches)) {
            switch ($type) {
                case "bool":
                case "boolean":
                    if ($paramType === "bool" || $paramType === "boolean") {
                        return (bool)$paramValue;
                    }
                    break;
                case "int":
                case "integer":
                    if ($paramType === "integer" || $paramType === "int") {
                        return (int)$paramValue;
                    }
                    break;
            }
            if ($paramType === $type) {
                switch ($type) {
                    case "string":
                        return (string)$paramValue;
                    case "float":
                        return (float)$paramValue;
                    default:
                        if (!is_object($paramValue)) {
                            throw new \Exception("Expected object");
                        }
                        if (!$paramValue instanceof $type) {
                            throw new \Exception("Expected object of type $type");
                        }
                        return $paramValue;
                }
            }
            throw new \Exception("Type mismatch, expected $type but got $paramType");
        }

        $elementType = $matches[1];

        // TODO: Not currently supported.
        $index = $matches[2];

        $result = array();
        if (!is_array($paramValue)) {
            $paramValue = array(array($paramType,
                                      $paramValue
                                )
            );
        }
        foreach ($paramValue as $element) {
            $result[] = $this->_collapseAndCheckType($element, $elementType);
        }
        return $result;

    }

}

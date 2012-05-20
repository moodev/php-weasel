<?php
namespace PhpAnnotation;

class DocblockParser
{

    protected $annotations;

    public function parse($docBlock, $location, $namespaces) {
        return $this->_parse($docBlock, $location, $namespaces);
    }

    public function __construct(AnnotationConfigurator $annotations) {
        $this->annotations = $annotations;
    }

    protected function _getAnnotation($name, $namespaces) {

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

    protected function _parse($input, $location, $namespaces) {
        $lexer = new DocblockLexer($input);
        return $this->_DocBlock($lexer, $location, $namespaces);
    }

    protected function _DocBlock(DocblockLexer $lexer, $location, $namespaces) {
        $annotations = array();
        while ($lexer->seekToType(DocblockLexer::T_AT)) {
            $annotation = $this->_Annotation($lexer, $location, $namespaces);
            if (isset($annotation)) {
                $annotations[$annotation[0]][] = $annotation[1];
            }
        }
        return $annotations;
    }

    protected function _Array(DocblockLexer $lexer, $location, $namespaces) {

        $elements = array();

        while (($next = $lexer->peek()) !== DocblockLexer::T_CLOSE_BRACE) {
            $elements[] = $this->_ParamValue($lexer, $location, $namespaces);

            if ($lexer->peek() === DocblockLexer::T_CLOSE_BRACE) {
                break;
            }
            $lexer->readAndCheck(DocblockLexer::T_COMMA);
        }
        $lexer->readAndCheck(DocblockLexer::T_CLOSE_BRACE);
        return $elements;
    }

    protected function _ParamValue(DocblockLexer $lexer, $location, $namespaces) {
        $cur = $lexer->read();
        switch ($cur["type"]) {
            case DocblockLexer::T_INTEGER:
                $param = array('integer', $cur['token']);
                break;
            case DocblockLexer::T_FLOAT:
                $param = array('float', $cur['token']);
                break;
            case DocblockLexer::T_BOOLEAN:
                $param = array('boolean', $cur['token']);
                break;
            case DocblockLexer::T_QUOTED_STRING:
                $param = array('string', $cur['token']);
                break;
            case DocblockLexer::T_AT:
                $object = $this->_Annotation($lexer, $location, $namespaces);
                return $object;
                break;
            case DocblockLexer::T_OPEN_BRACE:
                $array = $this->_Array($lexer, $location, $namespaces);
                return array('array', $array);
            case DocblockLexer::T_IDENTIFIER:
                // Might be an enum then...
                $enum = $this->_Enum($lexer, $location, $namespaces);
                return $enum;
            default:
                throw new \Exception("Parse error got {$cur["type"]} ({$cur['token']})");
        }
        return $param;
    }

    protected function _NamedParam(DocblockLexer $lexer, $location, $namespaces) {
        $nameToken = $lexer->readAndCheck(DocblockLexer::T_IDENTIFIER);

        $lexer->readAndCheck(DocblockLexer::T_EQUAL);

        $value = $this->_ParamValue($lexer, $location, $namespaces);

        return array($nameToken["token"], $value);

    }

    protected function _ClassName(DocblockLexer $lexer) {
        $next = $lexer->read();

        $class = '';
        if ($next['type'] === DocblockLexer::T_BACKSLASH) {
            $class .= '\\';
            $next = $lexer->readAndCheck(DocblockLexer::T_IDENTIFIER);
        }

        $class .= $next['token'];

        while ($lexer->peek() === DocblockLexer::T_BACKSLASH) {
            $class .= '\\';
            $part = $lexer->readAndCheck(DocblockLexer::T_IDENTIFIER);
            $class .= $part['token'];
        }
        return $class;
    }

    protected function _Enum(DocblockLexer $lexer, $location, $namespaces) {
        $class = $this->_ClassName($lexer);
        $meta = $this->_getAnnotation($class, $namespaces);
        if (!$meta) {
            throw new \Exception("Unable to resolve enum class $class");
        }

        $lexer->readAndCheck(DocblockLexer::T_DOT);
        $enum = $lexer->readAndCheck(DocblockLexer::T_IDENTIFIER);
        $lexer->readAndCheck(DocblockLexer::T_DOT);
        $index = $lexer->readAndCheck(DocblockLexer::T_IDENTIFIER);

        if (!isset($meta["enums"][$enum][$index])) {
            throw new \Exception("Unable to lookup enum value for $class : $enum : $index");
        }

        return array("integer", $meta["enums"][$enum][$index]);

    }

    protected function _Annotation(DocblockLexer $lexer, $location, $namespaces) {

        $identifier = $this->_ClassName($lexer);

        $meta = $this->_getAnnotation($identifier, $namespaces);
        if (!$meta) {
            return null;
        }

        if (isset($meta["on"]) && !in_array($location, $meta["on"])) {
            throw new \Exception("Found annotation in wrong location");
        }

        if ($lexer->peek() === DocblockLexer::T_OPEN_PAREN) {
            $lexer->read();

            $anonParams = array();
            $namedParams = array();

            while (($next = $lexer->peek()) !== DocblockLexer::T_CLOSE_PAREN) {
                if ($next === null) {
                    throw new \Exception('Unmatched parentheses');
                }
                if ($next === DocblockLexer::T_IDENTIFIER && $lexer->peek(2) === DocblockLexer::T_EQUAL) {
                    list($name, $param) = $this->_NamedParam($lexer, $meta['class'], $namespaces);
                    $namedParams[$name] = $param;
                } elseif ($next === DocblockLexer::T_COMMA) {
                    $lexer->readAndCheck(DocblockLexer::T_COMMA);
                } else {
                    $anonParams[] = $this->_ParamValue($lexer, $meta['class'], $namespaces);
                }
            }
            if (!empty($anonParams) && !empty($namedParams)) {
                throw new \Exception('Named or anonymous params, pick one.');
            }

            $lexer->readAndCheck(DocblockLexer::T_CLOSE_PAREN);

        }

        $class = $meta['class'];

        if (isset($meta['creatorMethod'])) {
            // There's a creator method to call

            $expectedParams = isset($meta['creatorParams']) ? $meta['creatorParams'] : array();
            $actualParams = array();
            if (!empty($anonParams)) {
                if (count($anonParams) > count($expectedParams)) {
                    throw new \Exception("Too many parameters");
                }
                reset($anonParams);
                foreach($expectedParams as $paramConfig) {
                    $param = each($anonParams);
                    $param = ($param === false) ? null : $param['value'];
                    if ($param === null) {
                        if (isset($paramConfig['required']) && $paramConfig['required'] === true) {
                            throw new \Exception('Missing required parameter ' . $paramConfig['name']);
                        }
                        $actualParams[] = null;
                    } else {
                        $actualParams[] = $this->_collapseAndCheckType($param, $paramConfig['type']);
                    }
                }
            } elseif (!empty($namedParams)) {
                foreach($expectedParams as $paramConfig) {
                    if (!isset($namedParams[$paramConfig['name']])) {
                        if (isset($paramConfig['required']) && $paramConfig['required'] === true) {
                            throw new \Exception('Missing required parameter ' . $paramConfig['name']);
                        }
                        $actualParams[] = null;
                    } else {
                        $actualParams[] = $this->_collapseAndCheckType($namedParams[$paramConfig['name']], $paramConfig['type']);
                    }
                }
            }

            if ($meta['creatorMethod'] === '__construct') {
                $reflectionClass = new \ReflectionClass($class);
                $annotation = $reflectionClass->newInstanceArgs($actualParams);
            } else {
                $method = $meta['creatorMethod'];
                $reflectionMethod = new \ReflectionMethod($class, $method);
                $annotation = $reflectionMethod->invokeArgs(null, $actualParams);
            }
        } else {
            $annotation = new $class();
        }

        if (!empty($namedParams) && isset($meta['properties'])) {
            foreach ($meta['properties'] as $name => $type) {
                if (isset($namedParams[$name])) {
                    $annotation->$name = $this->_collapseAndCheckType($namedParams[$name], $type);
                }
            }
        }

        return array($class, $annotation);
    }

    protected function _collapseAndCheckType($param, $type) {
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
            $paramValue = array(array($paramType, $paramValue));
        }
        foreach ($paramValue as $element) {
            $result[] = $this->_collapseAndCheckType($element, $elementType);
        }
        return $result;

    }

}

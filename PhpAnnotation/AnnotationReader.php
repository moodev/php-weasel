<?php
namespace PhpAnnotation;

/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12/05/12
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */
class AnnotationReader
{

    /**
     * @var \ReflectionClass
     */
    protected $class;

    protected $classAnnotations = null;
    protected $methodAnnotations = null;
    protected $propertyAnnotations = null;

    protected $propertyGetters = null;
    protected $propertySetters = null;

    protected $configurator = null;

    public function __construct(\ReflectionClass $class)
    {
        $this->class = $class;
        $this->configurator = new AnnotationConfigurator();
    }

    public function getClassAnnotations()
    {
        if (isset($this->classAnnotations)) {
            return $this->classAnnotations;
        }

        $docblock = $this->class->getDocComment();
        $this->classAnnotations = $this->_parse($docblock);
        return $this->classAnnotations;
    }

    public function getClassAnnotation($meta)
    {

    }

    public function getMethodAnnotations($method)
    {

    }

    public function getMethodAnnotation($method, $meta)
    {

    }

    public function getPropertyAnnotations($property)
    {

    }

    public function getPropertyAnnotation($property, $meta)
    {

    }

    public function getGetterForProperty($property)
    {

    }

    public function getSetterForProperty($property)
    {

    }

    protected function _getAnnotation($name) {
        if ($name[0] !== '\\') {
            // ffs
        }
        return $this->configurator->get($name);
    }

    protected function _parse($input) {
        $lexer = new AnnotationLexer($input);
        return $this->_DocBlock($lexer);
    }

    protected function _DocBlock(AnnotationLexer $lexer) {
        $annotations = array();
        while ($lexer->seekToType(AnnotationLexer::T_AT)) {
            $annotation = $this->_Annotation($lexer);
            if (isset($annotation)) {
                $annotations[$annotation[0]] = $annotation[1];
            }
        }
        return $annotations;
    }

    protected function _Array(AnnotationLexer $lexer) {

        $elements = array();

        while (($next = $lexer->peek()) !== AnnotationLexer::T_CLOSE_BRACE) {
            $elements[] = $this->_ParamValue($lexer);

            if ($lexer->peek() === AnnotationLexer::T_CLOSE_BRACE) {
                break;
            }
            $lexer->readAndCheck(AnnotationLexer::T_COMMA);
        }
        return $elements;
    }

    protected function _ParamValue(AnnotationLexer $lexer) {
        $cur = $lexer->read();
        switch ($cur["type"]) {
            case AnnotationLexer::T_INTEGER:
            case AnnotationLexer::T_FLOAT:
            case AnnotationLexer::T_BOOLEAN:
            case AnnotationLexer::T_QUOTED_STRING:
                $param = array($cur['type'], $cur['token']);
                break;
            case AnnotationLexer::T_AT:
                $object = $this->_Annotation($lexer);
                return array(get_class($object), $object);
                break;
            case AnnotationLexer::T_OPEN_BRACE:
                $array = $this->_Array($lexer);
                return array('array', $array);
            default:
                throw new \Exception('Parse error');
        }
        return $param;
    }

    protected function _NamedParam(AnnotationLexer $lexer) {
        $nameToken = $lexer->read(AnnotationLexer::T_IDENTIFIER);

        $lexer->readAndCheck(AnnotationLexer::T_EQUAL);

        $value = $this->_ParamValue($lexer);

        return array($nameToken["token"], $value);

    }

    protected function _ClassName(AnnotationLexer $lexer) {
        $next = $lexer->read();

        $class = '';
        if ($next['type'] === AnnotationLexer::T_BACKSLASH) {
            $class .= '\\';
            $next = $lexer->readAndCheck(AnnotationLexer::T_IDENTIFIER);
        }

        $class .= $next['token'];

        while ($lexer->peek() === AnnotationLexer::T_BACKSLASH) {
            $class .= '\\';
            $part = $lexer->readAndCheck(AnnotationLexer::T_IDENTIFIER);
            $class .= $part['token'];
        }
        return $class;
    }

    protected function _Annotation(AnnotationLexer $lexer) {
        $identifier = $this->_ClassName($lexer);

        $meta = $this->_getAnnotation($identifier);
        if (!$meta) {
            return null;
        }

        if ($lexer->peek() === AnnotationLexer::T_OPEN_PAREN) {
            $lexer->read();

            $anonParams = array();
            $namedParams = array();

            while (($next = $lexer->peek()) !== AnnotationLexer::T_CLOSE_PAREN) {
                if ($next === null) {
                    throw new \Exception('Unmatched parentheses');
                }
                if ($next === AnnotationLexer::T_IDENTIFIER) {
                    list($name, $param) = $this->_NamedParam($lexer);
                    $namedParams[$name] = $param;
                } else {
                    $anonParams[] = $this->_ParamValue($lexer);
                }
            }
            if (!empty($anonParams) && !empty($namedParams)) {
                throw new \Exception('Named or anonymous params, pick one.');
            }

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
                    if ($param === false) {
                        if ($paramConfig['required'] === true) {
                            throw new \Exception('Missing required parameter ' . $paramConfig['name']);
                        }
                        $actualParams[] = $this->_collapseAndCheckType($param, $paramConfig['type']);
                    }
                }
            } elseif (!empty($namedParams)) {
                foreach($expectedParams as $paramConfig) {
                    if (!isset($namedParams[$paramConfig['name']])) {
                        if ($paramConfig['required'] === true) {
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

        if (!empty($namedParams)) {
            // TODO: Deal with properties
        }

        return array($class, $annotation);
    }

    protected function _collapseAndCheckType($param, $type) {
        list($paramType, $paramValue) = $param;
        if ($paramType !== 'array') {
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

        $matches = array();
        if (!preg_match('/^(.*)\\[(int|integer|string|bool|boolean|float|)\\]$/i', $type, $matches)) {
            throw new \Exception("Unable to parse type $type as an array type");
        }
        $elementType = $matches[1];

        // TODO: Not currently supported.
        $index = $matches[2];

        $result = array();
        foreach ($paramValue as $element) {
            $result[] = $this->_collapseAndCheckType($element, $elementType);
        }
        return $result;

    }

}

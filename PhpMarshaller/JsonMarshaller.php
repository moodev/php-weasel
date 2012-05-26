<?php
namespace PhpMarshaller;

class JsonMarshaller
{

    /**
     * @var \PhpMarshaller\Config\ConfigProvider
     */
    protected $configProvider;


    public function __construct(\PhpMarshaller\Config\ConfigProvider $configProvider) {
        $this->configProvider = $configProvider;
    }

    public function readString($string, $class) {
        $decoded = json_decode($string, true);
        return $this->_decodeClass($decoded, $class);
    }


    public function writeString($object) {

        return json_encode($this->_encodeObject($object), JSON_FORCE_OBJECT);
    }

    protected function _encodeObject($object) {
        $class = get_class($object);

        $classconfig = $this->configProvider->getConfig($class);
        $config = $classconfig->serialization;

        $result = array();
        foreach ($config->properties as $key => $propConfig) {

            $value = null;
            if ($propConfig instanceof Config\Serialization\DirectSerialization) {
                /**
                 * @var Config\Serialization\DirectSerialization $propConfig
                 */

                $prop = $propConfig->property;
                $value = $object->$prop;

            } elseif ($propConfig instanceof Config\Serialization\GetterSerialization) {
                /**
                 * @var Config\Serialization\GetterSerialization $propConfig
                 */

                $meth = $propConfig->method;
                $value = $object->$meth();
            }

            // TODO polymorphism

            switch ($propConfig->include) {
                case Config\Serialization\ClassSerialization::INCLUDE_NON_EMPTY:
                    if (empty($value)) {
                        continue 2;
                    }
                    break;
                case Config\Serialization\ClassSerialization::INCLUDE_NON_DEFAULT:
                    throw new \Exception("Not currently supported");
                case Config\Serialization\ClassSerialization::INCLUDE_NON_NULL:
                    if (is_null($value)) {
                        continue 2;
                    }
            }

            $result[$key] = $this->_encodeValue($value, $propConfig->type);
        }

        return $result;

    }


    protected function _instantiateClassFromPropertyCreator($array, $class, Config\Deserialization\PropertyCreator $creator) {
        $args = array();
        foreach ($creator->params as $param) {
            $val = null;
            if (isset($array[$param->name])) {
                $val = $this->_decodeValue($array[$param->name], $param->type);
            }
            $args[] = $val;
        }

        // TODO: Avoid reflectors up to N (4?) args
        if ($creator->method === '__construct') {

            $rClass = new \ReflectionClass($class);
            return $rClass->newInstanceArgs($args);
        } else {
            $rMethod = new \ReflectionMethod($class, $creator->method);
            return $rMethod->invokeArgs(null, $args);
        }
    }

    protected function _decodeClass($array, $class) {
        $classconfig = $this->configProvider->getConfig($class);
        // Todo: polymorphism
        $deconfig = $classconfig->deserialization;

        $ignoreProperties = array();
        if ($deconfig->creator) {
            if ($deconfig->creator instanceof Config\Deserialization\DelegateCreator) {
                return new $class($array);
            } else {
                $creator = $deconfig->creator;
                /**
                 * @var Config\Deserialization\PropertyCreator $creator
                 */
                $object = $this->_instantiateClassFromPropertyCreator($array, $class, $creator);
                foreach ($creator->params as $param) {
                    $ignoreProperties[$param->name] = true;
                }
            }
        } else {
            $object = new $class();
        }

        if (!empty($deconfig->ignoreProperties)) {
            foreach ($deconfig->ignoreProperties as $ignore) {
                $ignoreProperties[$ignore] = true;
            }
        }

        foreach ($array as $key => $value) {
            if (isset($deconfig->properties[$key])) {
                $propConfig = $deconfig->properties[$key];

                $decodedValue = $this->_decodeValue($value, $propConfig->type);

                if ($propConfig instanceof Config\Deserialization\DirectDeserialization) {
                    /**
                     * @var Config\Deserialization\DirectDeserialization $propConfig
                     */

                    $prop = $propConfig->property;

                    $object->$prop = $decodedValue;

                } elseif ($propConfig instanceof Config\Deserialization\SetterDeserialization) {
                    /**
                     * @var Config\Deserialization\SetterDeserialization $propConfig
                     */

                    $meth = $propConfig->method;

                    $object->$meth($decodedValue);

                }
            } elseif (!$deconfig->ignoreUnknown) {
                if (!isset($ignoreProperties[$key])) {
                    trigger_error("Unknown property: $key", E_USER_WARNING);
                }
            }
        }

        return $object;


    }

    protected function _decodeValue($value, $type) {
        // TODO: should this be more tolerant of stupidity?
        $matches = array();
        if (!isset($value)) {
            return null;
        }
        if (!preg_match('/^(.*)\\[(int|integer|string|bool|boolean|float|)\\]$/i', $type, $matches)) {
            switch ($type) {
                case "bool":
                case "boolean":
                    if (is_bool($value)) {
                        return (bool)$value;
                    }
                    if ($value === "true" || $value === 1) {
                        return true;
                    }
                    if ($value === "false" || $value === 0) {
                        return false;
                    }
                    throw new \Exception("Type error");
                    break;
                case "int":
                case "integer":
                    if (!is_numeric($value)) {
                        throw new \Exception("Type error, expected numeric but got " . $value);
                    }
                    return (int)$value;
                    break;
                case "string":
                    if (!is_string($value)) {
                        throw new \Exception("Type error, expected string but got " . gettype($value));
                    }
                    return (string)$value;
                case "float":
                    if (!is_numeric($value)) {
                        throw new \Exception("Type error, expected numeric but got " . $value);
                    }
                    return (float)$value;
                default:
                    if (!is_array($value)) {
                        throw new \Exception("Expected array but found something else (or type $type is bad) got: " . gettype($value));
                    }
                    return $this->_decodeClass($value, $type);
            }
        }

        $elementType = $matches[1];

        $indexType = $matches[2];
        if (empty($indexType)) {
            $indexType = "int";
        }

        $result = array();
        if (!is_array($value)) {
            $value = array($value);
        }
        foreach ($value as $key => $element) {
            $result[$this->_decodeValue($key, $indexType)] = $this->_decodeValue($element, $elementType);
        }
        return $result;

    }

    protected function _encodeValue($value, $type) {
        $matches = array();
        if (!isset($value)) {
            return null;
        }
        if (!preg_match('/^(.*)\\[(int|integer|string|bool|boolean|float|)\\]$/i', $type, $matches)) {
            switch ($type) {
                case "bool":
                case "boolean":
                    if (!is_bool($value)) {
                        throw new \Exception("Type error");
                    }
                    return (bool)$value;
                    break;
                case "int":
                case "integer":
                    if (!is_int($value)) {
                        throw new \Exception("Type error");
                    }
                    return (int)$value;
                    break;
                case "string":
                    if (!is_string($value)) {
                        throw new \Exception("Type error");
                    }
                    return (string)$value;
                case "float":
                    if (!is_float($value)) {
                        throw new \Exception("Type error");
                    }
                    return (float)$value;
                default:
                    if (!is_object($value)) {
                        throw new \Exception("Expected object but found something else (or type $type is bad)");
                    }
                    return $this->_encodeObject($value);
            }
        }

        $elementType = $matches[1];

        $indexType = $matches[2];
        if (empty($indexType)) {
            $indexType = "int";
        }

        $result = array();
        if (!is_array($value)) {
            $value = array($value);
        }
        foreach ($value as $key => $element) {
            $result[$this->_encodeValue($key, $indexType)] = $this->_encodeValue($element, $elementType);
        }
        return $result;

    }
}

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

            // TODO: handle nulls.

            // TODO polymorphism

            $result[$key] = $this->_encodeValue($value, $propConfig->type);
        }

        return $result;

    }


    protected function _decodeClass($array, $class) {
        $classconfig = $this->configProvider->getConfig($class);
        // Todo: polymorphism
        $deconfig = $classconfig->deserialization;

        // Todo: creators
        $object = new $class();

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
                $object->$key = $value;
            } elseif (!$deconfig->ignoreUnknown) {
                if (!in_array($key, $deconfig->ignoreProperties)) {
                    // TODO: lob warning somewhere
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
                    if ($value === "true") {
                        return true;
                    }
                    if ($value === "false") {
                        return false;
                    }
                    throw new \Exception("Type error");
                    break;
                case "int":
                case "integer":
                    if (!is_numeric($value)) {
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
                    if (!is_numeric($value)) {
                        throw new \Exception("Type error");
                    }
                    return (float)$value;
                default:
                    if (!is_array($value)) {
                        throw new \Exception("Expected array but found something else (or type $type is bad)");
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

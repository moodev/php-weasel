<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

use Weasel\Common\Utils\ReflectionUtils;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;
use InvalidArgumentException;
use Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\Types\OldTypeWrapper;
use ReflectionClass;

class JsonMapper
{

    /**
     * @var \Weasel\JsonMarshaller\Config\JsonConfigProvider The source of our configuration
     */
    protected $configProvider;

    /**
     * @var Types\JsonType[] Array mapping type names to their handlers
     */
    protected $typeHandlers = array();

    /**
     * Setup a JsonMapper from a config provider
     * @param Config\JsonConfigProvider $configProvider
     */
    public function __construct(\Weasel\JsonMarshaller\Config\JsonConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
        $this->_registerBuiltInTypes();
    }

    /**
     * Setup the types we consider "built-in".
     */
    protected function _registerBuiltInTypes()
    {
        $this->registerJsonType("boolean", new Types\BoolType(), array("bool"));
        $this->registerJsonType("float", new Types\FloatType());
        $this->registerJsonType("integer", new Types\IntType(), array("int"));
        $this->registerJsonType("string", new Types\StringType());
        $this->registerJsonType("datetime", new Types\DateTimeType());
    }

    /**
     * Given a string of JSON, decode it into an instance of the named $class.
     * @param string $string JSON string containing an object
     * @param string $type Type to deserialize to.
     * @throws \InvalidArgumentException
     * @return mixed A populated instance of $class
     */
    public function readString($string, $type)
    {
        $decoded = json_decode($string, true);
        if ($decoded === null) {
            throw new InvalidArgumentException("Unable to decode JSON: $string");
        }
        return $this->_decodeValue($decoded, $type);
    }

    /**
     * Given a string containing a JSON array decode it into an array of the named $class.
     * @param string $string JSON string containing an array
     * @param string $class Full namespaced name of the class this JSON array contains
     * @return array Array of populated $class instances
     * @deprecated Use readString with an array type.
     */
    public function readArray($string, $class)
    {
        return $this->readString($string, $class . '[]');
    }

    protected function _guessArrayType(array $data)
    {
        $keyType = null;
        $valueType = null;
        $isMap = false;
        $i = 0;
        foreach ($data as $key => $value) {
            if ($keyType !== "string") {
                if (is_int($key) || ctype_digit($key)) {
                    $keyType = "integer";
                    if ($key !== $i) {
                        // Non sequential keys, it's not an "array", it's a "map"
                        $isMap = true;
                    }
                } else {
                    $keyType = "string";
                    $isMap = true;
                }
            }

            $cValueType = gettype($value);
            if ($cValueType === "array") {
                $cValueType = $this->_guessArrayType($value);
            } elseif ($cValueType === "object") {
                if ($valueType && !class_exists($valueType)) {
                    throw new InvalidArgumentException("Unable to guess consistent types. Hoped for $valueType but found $cValueType");
                }
                $cValueType = get_class($value);
                if ($valueType) {
                    $cValueType = $this->_findCommonBaseClass($valueType, $cValueType);
                }
            }
            if (!$valueType || $valueType === $cValueType) {
                $valueType = $cValueType;
            } else {
                switch ($cValueType) {
                    case "integer":
                        switch ($valueType) {
                            case "double":
                            case "string":
                                break;
                            default:
                                throw new InvalidArgumentException("Unable to guess consistent types. Hoped for $valueType but found $cValueType");
                        }
                        break;
                    case "double":
                        switch ($valueType) {
                            case "integer":
                                $valueType = $cValueType;
                                break;
                            case "string":
                                break;
                            default:
                                throw new InvalidArgumentException("Unable to guess consistent types. Hoped for $valueType but found $cValueType");
                        }
                        break;
                    case "string":
                        switch ($valueType) {
                            case "integer":
                            case "double":
                                $valueType = "string";
                                break;
                            default:
                                throw new InvalidArgumentException("Unable to guess consistent types. Hoped for $valueType but found $cValueType");
                        }
                        break;
                    default:
                        throw new InvalidArgumentException("Unable to guess consistent types. Hoped for $valueType but found $cValueType");
                }
            }

            $i++;
        }
        return $valueType . '[' . ($isMap ? $keyType : '') . ']';

    }

    protected function _findCommonBaseClass($classA, $classB)
    {
        if ($classA === $classB) {
            return $classA;
        }

        $rca = new ReflectionClass($classA);
        $rcb = new ReflectionClass($classB);

        $rcap = array();
        $cur = $rca;
        do {
            $rcap[$cur->getName()] = true;
        } while ($cur = $cur->getParentClass());

        $cur = $rcb;
        do {
            if (isset($rcap[$cur->getName()])) {
                return $cur->getName();
            }
        } while ($cur = $cur->getParentClass());

        throw new \RuntimeException("Unable to find common base class for $classA and $classB");

    }


    protected function _guessType($data)
    {
        $type = gettype($data);

        switch ($type) {
            case "array":
                $type = $this->_guessArrayType($data);
                break;
            case "object":
                $type = get_class($data);
                break;
            case "double":
                $type = "float";
                break;
            case "string":
            case "integer":
                break;
            case "NULL":
                $type = "string";
                break;
            default:
                throw new InvalidArgumentException("Unknown type: $type");
        }

        return $type;
    }

    /**
     * Serialize an data to a string of JSON.
     * @param mixed $data Data to serialize
     * @param string $type Type of the data being encoded. If not provided then this will be guessed.
     *                      Guessing may not work reliably with complex array structures, or if $data is a subclass
     *                      of the class you actually want to serialize as.
     * @return string The JSON
     */
    public function writeString($data, $type = null)
    {
        if (!isset($type)) {
            $type = $this->_guessType($data);
        }
        return $this->_encodeValue($data, $type);
    }

    /**
     * Serialize an object to an array suitable for passing to json_encode.
     * This used to be useful. Now all it does is call json_decode on the result of a writeString().
     * It's probably not what you want to use.
     * @param mixed $data The data to serialize
     * @param string $type Type of the data being encoded. If not provided then this will be guessed.
     *                      Guessing may not work reliably with complex array structures, or if $data is a subclass
     *                      of the class you actually want to serialize as.
     * @return array An array suitable for json_encode.
     * @deprecated This is no longer useful since all it does is call json_decode on the result of a writeString() operation.
     */
    public function writeArray($data, $type = null)
    {
        return json_decode($this->writeString($data, $type), true);
    }

    /**
     * Encode an object to an array representation based on our configuration.
     * @param object $object Object to serialize.
     * @param Config\Serialization\TypeInfo $typeInfo TypeInfo to override that from the class config, used when a
     * property has TypeInfo associated with it.
     * @param string $type Type this is being encoded into.
     * @throws Exception\InvalidTypeException
     * @throws \Exception
     * @return array
     */
    protected function _encodeObject($object, $typeInfo = null, $type = null)
    {
        $class = get_class($object);
        $classconfig = $this->configProvider->getConfig($class);
        if (!isset($classconfig)) {
            throw new \Exception("No configuration found for class $class");
        }
        $config = $classconfig->serialization;
        if (!isset($config)) {
            throw new \Exception("No serialization configuration found for class $class");
        }

        $properties = array();
        if (isset($config->anyGetter)) {
            $method = $config->anyGetter;
            $extras = $object->$method();
            if (isset($extras)) {
                if (!is_array($properties)) {
                    throw new InvalidTypeException("array", $properties);
                }
                foreach ($extras as $key => $value) {
                    $properties[$key] = json_encode($value);
                }
            }
        }

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
            } else {
                throw new \Exception("No idea how to serialize something with the given config");
            }


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

            $properties[$key] = $this->_encodeValue($value, $propConfig->type, $propConfig->typeInfo);
            if (is_object($value) &&
                $propConfig->typeInfo &&
                $propConfig->typeInfo->typeInfoAs === Config\Serialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY
            ) {
                // We need to store the type of the encoded object on ourselves
                $propClass = get_class($object);
                if (isset($typeInfo->subTypes[$propClass])) {
                    $classId = $typeInfo->subTypes[$propClass];

                    $properties[$propConfig->typeInfo->typeInfoProperty] = $classId;
                }

            }
        }

        if (!$typeInfo) {
            // Typeinfo wasn't passed in on the property, we need to see if we have any to load
            if (isset($type)) {
                // We load typeinfo stuff from our base type, not the actual class we're serializing.
                $parentConfig = $this->configProvider->getConfig($type);
                if (isset($parentConfig)) {
                    $typeInfo = $parentConfig->serialization->typeInfo;
                }
            }
        }

        if ($typeInfo) {
            // We need to encode information about what type we are somewhere.
            // First off work out what name we go by (what value should be stored wherever it is)
            switch ($typeInfo->typeInfo) {
                case Config\Serialization\TypeInfo::TI_USE_CLASS:
                case Config\Serialization\TypeInfo::TI_USE_MINIMAL_CLASS:
                case Config\Serialization\TypeInfo::TI_USE_NAME:
                    // In all these cases the typeInfo subTypes config should contain the mapping from our class to
                    // whatever value we should be called by.
                    if (!isset($typeInfo->subTypes[$class])) {
                        break;
                    }
                    $classId = $typeInfo->subTypes[$class];
                    break;
                case Config\Serialization\TypeInfo::TI_USE_CUSTOM: // TODO
                default:
                    throw new \Exception("Unsupported type info at class level");
            }
            // Now where should we put this information?
            switch ($typeInfo->typeInfoAs) {
                case Config\Serialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY:
                    // The property is on the object that contains us, so this is SEP.
                    break;
                case Config\Serialization\TypeInfo::TI_AS_PROPERTY:
                    // We're going to store the classId as a string on this object
                    if (!isset($classId)) {
                        break;
                    }
                    $property = $typeInfo->typeInfoProperty;
                    $properties[$property] = $this->_encodeValue($classId, "string");
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY:
                    // We're actually going to encase this encoded object in an array containing the classId.
                    if (!isset($classId)) {
                        break;
                    }
                    return '[' . $this->_encodeValue($classId, 'string') . ', ' . $this->_objectToJson($properties) . ']';
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    // Very similar yo the wrapper array case, but this time it's a map from the classId to the object.
                    if (!isset($classId)) {
                        break;
                    }
                    return '{' . $this->_encodeValue($classId, 'string') . ': ' . $this->_objectToJson($properties) . '}';
                    break;
                default:
                    throw new \Exception("Unsupported type info storage at class level");
            }
        }

        return $this->_objectToJson($properties);

    }

    protected function _objectToJson($properties)
    {
        $elements = array();
        foreach ($properties as $key => $property) {
            $elements[] = $this->_encodeValue($key, 'string') . ': ' . $property;
        }
        return '{' . implode(', ', $elements) . '}';
    }


    protected function _instantiateClassFromPropertyCreator($array,
                                                            $class,
                                                            Config\Deserialization\PropertyCreator $creator)
    {
        $args = array();
        foreach ($creator->params as $param) {
            $val = null;
            if (isset($array[$param->name])) {
                $val = $this->_decodeValue($array[$param->name], $param->type);
            }
            $args[] = $val;
        }

        if ($creator->method === '__construct') {
            return ReflectionUtils::instantiateClassByConstructor($class, $args);
        } else {
            return ReflectionUtils::invokeStaticMethod($class, $creator->method, $args);
        }
    }


    protected function _decodeClass($array, $class)
    {
        $classconfig = $this->configProvider->getConfig($class);
        if (!isset($classconfig)) {
            throw new \Exception("No configuration found for class $class");
        }

        $deconfig = $classconfig->deserialization;

        $canIgnoreProperties = array();
        if (isset($deconfig->typeInfo)) {
            $typeInfo = $deconfig->typeInfo;
            // First we need to work out what type to deserialize as
            if (!empty($typeInfo->defaultImpl)) {
                $class = $typeInfo->defaultImpl;
            }
            $typeId = null;
            switch ($typeInfo->typeInfoAs) {
                case Config\Deserialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY:
                case Config\Deserialization\TypeInfo::TI_AS_PROPERTY:
                    $property = $typeInfo->typeInfoProperty;
                    $canIgnoreProperties[$property] = true;
                    if (!isset($array[$property])) {
                        break;
                    }
                    $typeId = $array[$property];
                    if ($typeInfo->typeInfoVisible == false) {
                        unset($array[$property]);
                    }
                    break;
                case Config\Deserialization\TypeInfo::TI_AS_WRAPPER_ARRAY:
                    if (count($array) !== 2) {
                        throw new \Exception("Typeinfo is wrapper array, but array does not have exactly 2 elements");
                    }
                    $typeId = $array[0];
                    $array = $array[1];
                    break;
                case Config\Deserialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    if (count($array) !== 1) {
                        throw new \Exception("Typeinfo is wrapper object, but object does not have exactly one property");
                    }
                    list($typeId) = array_keys($array);
                    $array = array_shift($array);
                    break;
                default:
                    throw new \Exception("Unsupported type info storage at class level");
            }

            switch ($typeInfo->typeInfo) {
                case Config\Deserialization\TypeInfo::TI_USE_CLASS:
                case Config\Deserialization\TypeInfo::TI_USE_MINIMAL_CLASS:
                case Config\Deserialization\TypeInfo::TI_USE_NAME:
                    if (!isset($typeInfo->subTypes[$typeId])) {
                        break;
                    }
                    $class = $typeInfo->subTypes[$typeId];
                    break;
                case Config\Deserialization\TypeInfo::TI_USE_CUSTOM: // TODO
                default:
                    throw new \Exception("Unsupported type info at class level");
            }

        }
        $classconfig = $this->configProvider->getConfig($class);

        $deconfig = $classconfig->deserialization;

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
                    $canIgnoreProperties[$param->name] = true;
                }
            }
        } else {
            $object = new $class();
        }

        if (!empty($deconfig->ignoreProperties)) {
            foreach ($deconfig->ignoreProperties as $ignore) {
                $canIgnoreProperties[$ignore] = true;
            }
        }

        foreach ($array as $key => $value) {
            if (isset($deconfig->properties[$key])) {
                $propConfig = $deconfig->properties[$key];

                try {
                    $decodedValue = $this->_decodeValue($value, $propConfig->type);
                } catch (\Exception $e) {
                    throw new \Exception("Failed to decode property $key on $class", 0, $e);
                }

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
            } elseif (isset($deconfig->anySetter)) {
                $method = $deconfig->anySetter;
                $object->$method($key, $value);
            } elseif (!$deconfig->ignoreUnknown) {
                if (!isset($canIgnoreProperties[$key])) {
                    trigger_error("Unknown property: $key", E_USER_WARNING);
                }
            }
        }

        return $object;


    }

    protected function _parseType($type)
    {

        $matches = array();
        if (!preg_match('/^(.*)\\[([^\\]]*)\\]$/i', $type, $matches)) {
            if (isset($this->typeHandlers[$type])) {
                return array($type,
                    $this->typeHandlers[$type]
                );
            }
            return array("complex");
        }

        $elementType = $matches[1];

        $indexType = $matches[2];
        if (empty($indexType)) {
            return array("array", $elementType);
        }
        return array("map",
            $indexType,
            $elementType
        );
    }

    protected function _decodeValue($value, $type)
    {
        if (!isset($value)) {
            return null;
        }
        $typeData = $this->_parseType($type);
        switch (array_shift($typeData)) {
            case "complex":
                if (!is_array($value)) {
                    throw new InvalidTypeException($type, $value);
                }
                return $this->_decodeClass($value, $type);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case "array":
                array_unshift($typeData, 'int');
            case "map":
                list ($indexType, $elementType) = $typeData;
                $result = array();
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($value as $key => $element) {
                    $result[$this->_decodeValue($key, $indexType)] = $this->_decodeValue($element, $elementType);
                }
                return $result;
                break;
            default:
                /**
                 * @var $typeHandler Types\JsonType
                 */
                list ($typeHandler) = $typeData;
                return $typeHandler->decodeValue($value, $this);
        }

    }

    /**
     * @param mixed $value
     * @param string $type
     * @param Config\Serialization\TypeInfo $typeInfo
     * @throws Exception\InvalidTypeException
     * @return mixed
     */
    protected function _encodeValue($value, $type, $typeInfo = null)
    {
        if (!isset($value)) {
            return json_encode(null);
        }
        $typeData = $this->_parseType($type);
        switch (array_shift($typeData)) {
            case "complex":
                if (!is_object($value)) {
                    throw new InvalidTypeException($type, $value);
                }
                return $this->_encodeObject($value, $typeInfo, $type);
                break;
            case "array":
                list ($elementType) = $typeData;
                if (!is_array($value)) {
                    $value = array($value);
                }
                $elements = array();
                foreach ($value as $element) {
                    $elements[] = $this->_encodeValue($element, $elementType);
                }
                return '[' . implode(', ', $elements) . ']';
            case "map":
                list ($indexType, $elementType) = $typeData;
                if (!is_array($value)) {
                    $value = array($value);
                }
                $elements = array();
                foreach ($value as $key => $element) {
                    $elements[] = $this->_encodeValue($key, $indexType) . ': ' . $this->_encodeValue($element, $elementType);
                }
                return '{' . implode(', ', $elements) . '}';
            default:
                /**
                 * @var $typeHandler Types\JsonType
                 */
                list ($typeHandler) = $typeData;
                return $typeHandler->encodeValue($value, $this);

        }

    }


    /**
     * Register a custom type.
     * @param string $name
     * @param Types\JsonType $handler
     * @param string[] $aliases
     */
    public function registerJsonType($name, $handler, $aliases = array())
    {
        $this->typeHandlers[$name] = $handler;
        foreach ($aliases as $alias) {
            $this->typeHandlers[$alias] = $handler;
        }
    }

    /**
     * Register an old style custom type.
     * This is a compatibility handler and will be removed. Please use registerJsonType!
     * @Deprecated
     * @param string $name
     * @param Types\Type $handler
     * @param string[] $aliases
     */
    public function registerType($name, $handler, $aliases = array())
    {
        trigger_error("Types are deprecated, use JsonTypes through registerJsonType.", E_USER_DEPRECATED);
        $this->registerJsonType($name, new OldTypeWrapper($handler), $aliases);
    }
}

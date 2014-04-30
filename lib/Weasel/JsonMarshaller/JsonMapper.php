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
use Weasel\JsonMarshaller\Exception\JsonMarshallerException;
use Weasel\JsonMarshaller\Config\JsonConfigProvider;
use Weasel\JsonMarshaller\Utils\TypeParser;

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
     * @var bool Should we use strict mode unless told otherwise.
     */
    protected $strict = true;

    /**
     * Setup a JsonMapper from a config provider
     * @param Config\JsonConfigProvider $configProvider
     * @param bool $strict Default for strict mode.
     */
    public function __construct(JsonConfigProvider $configProvider, $strict = true)
    {
        $this->configProvider = $configProvider;
        $this->_registerBuiltInTypes();
        $this->strict = $strict;
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
     * @param bool $strict Use strict type checking. If false will not check any types. If true can be overridden on a property by property basis. If null use the default.
     * @throws \InvalidArgumentException
     * @return mixed A populated instance of $class
     */
    public function readString($string, $type, $strict = null)
    {
        if ($string === "null" || $string === "") {
            return null;
        }
        $decoded = json_decode($string, true);
        if ($decoded === null) {
            throw new InvalidArgumentException("Unable to decode JSON: $string");
        }
        if ($strict === null) {
            $strict = $this->strict;
        }
        return $this->_decodeValue($decoded, TypeParser::parseType($type, true), $strict);
    }

    /**
     * Given a string containing a JSON array decode it into an array of the named $class.
     * @param string $string JSON string containing an array
     * @param string $class Full namespaced name of the class this JSON array contains
     * @param bool $strict Use strict type checking. If false will not check any types. If true can be overridden on a property by property basis. If null use the default.
     * @return array Array of populated $class instances
     * @deprecated Use readString with an array type.
     */
    public function readArray($string, $class, $strict = null)
    {
        return $this->readString($string, $class . '[]', $strict);
    }

    protected function _guessType($data)
    {
        $type = gettype($data);

        switch ($type) {
            case "integer":
            case "string":
                break;
            case "double":
                $type = "float";
                break;
            case "object":
                $type = get_class($data);
                break;
            case "NULL":
                $type = "string";
                break;
            default:
                throw new InvalidArgumentException("Unable to guess type of data, please provide a type specification.");
        }

        return $type;
    }

    /**
     * Serialize an data to a string of JSON.
     * @param mixed $data Data to serialize
     * @param string $type Type of the data being encoded. If not provided then this will be guessed.
     *                      Guessing only works with primitives and simple objects.
     * @return string The JSON
     */
    public function writeString($data, $type = null)
    {
        if (!isset($type)) {
            $type = $this->_guessType($data);
        }
        return $this->_encodeValue($data, TypeParser::parseType($type, true));
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
                    $properties[$property] = $this->_encodeValue($classId, array(TypeParser::TYPE_SCALAR, "string"));
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY:
                    // We're actually going to encase this encoded object in an array containing the classId.
                    if (!isset($classId)) {
                        break;
                    }
                    return '[' . $this->_encodeValue($classId,
                        array(TypeParser::TYPE_SCALAR, 'string')) . ', ' . $this->_objectToJson($properties) . ']';
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    // Very similar yo the wrapper array case, but this time it's a map from the classId to the object.
                    if (!isset($classId)) {
                        break;
                    }
                    return '{' . $this->_encodeValue($classId,
                        array(TypeParser::TYPE_SCALAR, 'string')) . ': ' . $this->_objectToJson($properties) . '}';
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
            $elements[] = $this->_encodeValue($key, array(TypeParser::TYPE_SCALAR, 'string')) . ': ' . $property;
        }
        return '{' . implode(', ', $elements) . '}';
    }


    protected function _instantiateClassFromPropertyCreator($array,
                                                            $class,
                                                            Config\Deserialization\PropertyCreator $creator,
                                                            $strict)
    {
        $args = array();
        foreach ($creator->params as $param) {
            $val = null;
            if (isset($array[$param->name])) {
                $val = $this->_decodeValue($array[$param->name], $param->type, $param->strict && $strict);
            }
            $args[] = $val;
        }

        if ($creator->method === '__construct') {
            return ReflectionUtils::instantiateClassByConstructor($class, $args);
        } else {
            return ReflectionUtils::invokeStaticMethod($class, $creator->method, $args);
        }
    }


    protected function _decodeClass($array, $class, $strict)
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

        if ($classconfig == null) {
            throw new InvalidArgumentException("No config found to decode $class");
        }
        $deconfig = $classconfig->deserialization;

        if ($deconfig->creator) {
            if ($deconfig->creator instanceof Config\Deserialization\DelegateCreator) {
                return new $class($array);
            } else {
                $creator = $deconfig->creator;
                /**
                 * @var Config\Deserialization\PropertyCreator $creator
                 */
                $object = $this->_instantiateClassFromPropertyCreator($array, $class, $creator, $strict);
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
                    $decodedValue = $this->_decodeValue($value, $propConfig->type, $propConfig->strict && $strict);
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

    protected function _parseType($type) {
        if (!is_array($type)) {
            if (defined('E_USER_DEPRECATED')) {
                // TODO: need to handle serialized configs before we can properly deprecate this.
//                trigger_error("Use of unexpanded types is deprecated", E_USER_DEPRECATED);
            }
            // This is the really slow path.
            $type = TypeParser::parseType($type, false);
        }
        if ($type[0] == TypeParser::TYPE_SCALAR) {
            if (isset($this->typeHandlers[$type[1]])) {
                // Assumption: if there's a type handler for this type string, then it's the right thing to use.
                return array(TypeParser::TYPE_SCALAR, $type[1], $this->typeHandlers[$type[1]]);
            } else {
                return array(TypeParser::TYPE_OBJECT, $type[1]);
            }
        }
        return $type;
    }

    protected function _decodeKey($value, $type)
    {
        if (!isset($value)) {
            throw new JsonMarshallerException("Key values cannot be null");
        }
        $typeData = $this->_parseType($type);
        switch ($typeData[0]) {
            case TypeParser::TYPE_SCALAR:
                // Keys are always strings, however we will allow other types, and disable strict type checking.
                return $this->_decodeValue($value, $typeData, false);
            default:
                throw new JsonMarshallerException("Keys must be of type int or string, not " . $type);
        }
    }

    protected function _decodeValue($value, $type, $strict)
    {
        if (!isset($value)) {
            return null;
        }
        $typeData = $this->_parseType($type);
        switch (array_shift($typeData)) {
            case TypeParser::TYPE_OBJECT:
                if (!is_array($value)) {
                    throw new InvalidTypeException($typeData[0], $value);
                }
                return $this->_decodeClass($value, $typeData[0], $strict);
                break;
            /** @noinspection PhpMissingBreakStatementInspection */
            case TypeParser::TYPE_LIST:
            case TypeParser::TYPE_MAP:
                list ($indexType, $elementType) = $typeData;
                $result = array();
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($value as $key => $element) {
                    $result[$this->_decodeKey($key, $indexType)] = $this->_decodeValue($element, $elementType, $strict);
                }
                return $result;
                break;
            default:
                /**
                 * @var $typeHandler Types\JsonType
                 */
                list ($typeName, $typeHandler) = $typeData;
                return $typeHandler->decodeValue($value, $this, $strict);
        }

    }

    protected function _encodeKey($value, $type)
    {
        if (!isset($value)) {
            throw new JsonMarshallerException("Key values cannot be null");
        }
        $typeData = $this->_parseType($type);
        switch (array_shift($typeData)) {
            /** @noinspection PhpMissingBreakStatementInspection */
            case TypeParser::TYPE_SCALAR:
                return $this->_encodeValue($value, array(TypeParser::TYPE_SCALAR, "string"));
            default:
                throw new JsonMarshallerException("Keys must be of type int or string, not " . $type);
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
            case TypeParser::TYPE_OBJECT:
                if (!is_object($value)) {
                    throw new InvalidTypeException($typeData[0], $value);
                }
                return $this->_encodeObject($value, $typeInfo, $typeData[0]);
                break;
            case TypeParser::TYPE_LIST:
                list ($indexType, $elementType) = $typeData;
                if (!is_array($value)) {
                    $value = array($value);
                }
                $elements = array();
                foreach ($value as $element) {
                    $elements[] = $this->_encodeValue($element, $elementType);
                }
                return '[' . implode(', ', $elements) . ']';
            case TypeParser::TYPE_MAP:
                list ($indexType, $elementType) = $typeData;
                if (!is_array($value)) {
                    $value = array($value);
                }
                $elements = array();
                foreach ($value as $key => $element) {
                    $elements[] = $this->_encodeKey($key, $indexType) . ': ' . $this->_encodeValue($element,
                            $elementType);
                }
                return '{' . implode(', ', $elements) . '}';
            default:
                /**
                 * @var $typeHandler Types\JsonType
                 */
                list ($typeName, $typeHandler) = $typeData;
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

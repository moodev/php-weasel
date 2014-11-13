<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

use Weasel\Common\Utils\ReflectionUtils;
use Weasel\JsonMarshaller\Config\Serialization\ClassSerialization;
use Weasel\JsonMarshaller\Config\Type\ListType;
use Weasel\JsonMarshaller\Config\Type\MapType;
use Weasel\JsonMarshaller\Config\Type\ScalarType;
use Weasel\JsonMarshaller\Config\Type\Type;
use Weasel\JsonMarshaller\Config\Type\TypeParser;
use Weasel\JsonMarshaller\Exception\BadConfigurationException;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;
use InvalidArgumentException;
use Weasel\JsonMarshaller\Types;
use Weasel\JsonMarshaller\Types\JsonType;
use Weasel\JsonMarshaller\Types\OldTypeWrapper;
use Weasel\JsonMarshaller\Exception\JsonMarshallerException;
use Weasel\JsonMarshaller\Config\JsonConfigProvider;

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
        return $this->_decodeValue($decoded, $this->_parseTypeString($type), $strict);
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
        return $this->_encodeValue($data, $this->_parseTypeString($type));
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
        if (!$class) {
            throw new InvalidTypeException($type, $object);
        }
        $classconfig = $this->configProvider->getConfig($class);
        if (!isset($classconfig)) {
            throw new BadConfigurationException("No configuration found for class $class");
        }
        $config = $classconfig->serialization;
        if (!isset($config)) {
            throw new BadConfigurationException("No serialization configuration found for class $class");
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
            if ($propConfig->how == "direct") {
                /**
                 * @var Config\Serialization\DirectSerialization $propConfig
                 */

                $prop = $propConfig->property;
                $value = $object->$prop;

            } elseif ($propConfig->how == "getter") {
                /**
                 * @var Config\Serialization\GetterSerialization $propConfig
                 */

                $meth = $propConfig->method;
                $value = $object->$meth();
            } else {
                throw new BadConfigurationException("No idea how to serialize something with the given config");
            }

            switch ($propConfig->include) {
                case ClassSerialization::INCLUDE_ALWAYS:
                    break;
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

            $properties[$key] = $this->_encodeValue($value, $propConfig->realType, $propConfig->typeInfo);
            if ($propConfig->typeInfo &&
                $propConfig->typeInfo->typeInfoAs === Config\Serialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY
            ) {
                // We need to store the type of the encoded object on ourselves
                $propClass = get_class($object);
                if ($propClass && isset($typeInfo->subTypes[$propClass])) {
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
                throw new BadConfigurationException("Unsupported type info at class level");
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
                    $properties[$property] = $this->_encodeToString($classId);
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY:
                    // We're actually going to encase this encoded object in an array containing the classId.
                    if (!isset($classId)) {
                        break;
                    }
                    return '[' . $this->_encodeToString($classId) . ', ' . $this->_objectToJson($properties) . ']';
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    // Very similar yo the wrapper array case, but this time it's a map from the classId to the object.
                    if (!isset($classId)) {
                        break;
                    }
                    return '{' . $this->_encodeToString($classId) . ': ' . $this->_objectToJson($properties) . '}';
                    break;
                default:
                    throw new BadConfigurationException("Unsupported type info storage at class level");
            }
        }

        return $this->_objectToJson($properties);

    }

    protected function _objectToJson($properties)
    {
        $elements = array();
        foreach ($properties as $key => $property) {
            $elements[] = $this->_encodeToString($key) . ': ' . $property;
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
                $val = $this->_decodeValue($array[$param->name], $param->realType, $param->strict && $strict);
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
            throw new BadConfigurationException("No configuration found for class $class");
        }

        $deconfig = $classconfig->deserialization;

        $canIgnoreProperties = array();
        if (isset($deconfig->typeInfo)) {
            $typeInfo = $deconfig->typeInfo;
            // First we need to work out what type to deserialize as
            if (isset($typeInfo->defaultImpl)) {
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
                        throw new InvalidTypeException("array(2)", $array);
                    }
                    $typeId = $array[0];
                    $array = $array[1];
                    break;
                case Config\Deserialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    if (count($array) !== 1) {
                        throw new InvalidTypeException("array(1)", $array);
                    }
                    list($typeId) = array_keys($array);
                    $array = array_shift($array);
                    break;
                default:
                    throw new BadConfigurationException("Unsupported type info storage at class level");
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
                throw new BadConfigurationException("Unsupported type info at class level");
            }

        }
        $classconfig = $this->configProvider->getConfig($class);

        if (!isset($classconfig)) {
            throw new BadConfigurationException("No config found to decode $class");
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
            if (class_exists($class)) {
                $object = new $class();
            } else {
                throw new BadConfigurationException("Configured class $class is not an instantiable class.");
            }
        }

        if (isset($deconfig->ignoreProperties)) {
            foreach ($deconfig->ignoreProperties as $ignore) {
                $canIgnoreProperties[$ignore] = true;
            }
        }

        foreach ($array as $key => $value) {
            if (isset($deconfig->properties[$key])) {
                $propConfig = $deconfig->properties[$key];

                try {
                    $decodedValue = $this->_decodeValue($value, $propConfig->realType, $propConfig->strict && $strict);
                } catch (\Exception $e) {
                    throw new \Exception("Failed to decode property $key on $class", 0, $e);
                }

                if ($propConfig->how == "direct") {
                    /**
                     * @var Config\Deserialization\DirectDeserialization $propConfig
                     */

                    $prop = $propConfig->property;

                    $object->$prop = $decodedValue;

                } elseif ($propConfig->how == "setter") {
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

    /**
     * @param string $typeName
     * @return JsonType
     */
    protected function _getTypeHandler($typeName)
    {
        if (isset($this->typeHandlers[$typeName])) {
            // Assumption: if there's a type handler for this type string, then it's the right thing to use.
            return $this->typeHandlers[$typeName];
        }
        return null;
    }

    protected function _parseTypeString($type)
    {
        return TypeParser::parseTypeString($type);
    }

    /**
     * @param mixed $value
     * @param Type $type
     * @return mixed
     * @throws Exception\InvalidTypeException
     * @throws Exception\JsonMarshallerException
     */
    protected function _decodeKey($value, $type)
    {
        if (!isset($value)) {
            throw new JsonMarshallerException("Key values cannot be null");
        }
        if ($type->type != "scalar") {
            throw new JsonMarshallerException("Keys must be of type int or string, not " . $type);
        }
        // Keys are always strings, however we will allow other types, and disable strict type checking.
        return $this->_decodeValue($value, $type, false);
    }

    protected function _decodeValue($value, $type, $strict)
    {
        if (!isset($value)) {
            return null;
        }
        if ($type->type == "scalar") {
            $typeName = $type->typeName;
            if ($typeName == "integer" || $typeName == "string" || $typeName == "float" || $typeName == "boolean" || $typeName == "datetime") {
                return $this->typeHandlers[$typeName]->decodeValue($value, $this, $strict);
            }
            $typeHandler = $this->_getTypeHandler($typeName);
            if (isset($typeHandler)) {
                return $typeHandler->decodeValue($value, $this, $strict);
            }
            if (!is_array($value)) {
                throw new InvalidTypeException($typeName, $value);
            }
            return $this->_decodeClass($value, $typeName, $strict);
        }
        if ($type->type == "map" || $type->type == "list") {
            $indexType = $type->indexType;
            $elementType = $type->elementType;
            $result = array();
            if (!is_array($value)) {
                $value = array($value);
            }
            foreach ($value as $key => $element) {
                $result[$this->_decodeKey($key, $indexType)] = $this->_decodeValue($element, $elementType, $strict);
            }
            return $result;

        }
        return null;
    }

    protected function _encodeKey($value, $type)
    {
        if (!isset($value)) {
            throw new JsonMarshallerException("Key values cannot be null");
        }
        if (!isset($type->typeName)) {
            throw new JsonMarshallerException("Keys must be of type int or string, not " . $type);
        }
        return $this->_encodeToString($value);
    }

    protected function _encodeToString($value)
    {
        return $this->typeHandlers["string"]->encodeValue($value, $this);
    }

    /**
     * @param mixed $value
     * @param Type $type
     * @param Config\Serialization\TypeInfo $typeInfo
     * @throws Exception\InvalidTypeException
     * @return mixed
     */
    protected function _encodeValue($value, $type, $typeInfo = null)
    {
        if (!isset($value)) {
            return "null";
        }
        if ($type->type == "scalar") {
            /**
             * @var ScalarType $type
             */
            $typeName = $type->typeName;
            if ($typeName == "integer" || $typeName == "string" || $typeName == "float" || $typeName == "boolean" || $typeName == "datetime") {
                return $this->typeHandlers[$typeName]->encodeValue($value, $this);
            }
            $typeHandler = $this->_getTypeHandler($typeName);
            if (isset($typeHandler)) {
                return $typeHandler->encodeValue($value, $this);
            }
            return $this->_encodeObject($value, $typeInfo, $type->typeName);
        }
        if ($type->type == "list") {
            /**
             * @var ListType $type
             */
            $elementType = $type->elementType;
            if (!is_array($value)) {
                $value = array($value);
            }
            $elements = array();
            foreach ($value as $element) {
                $elements[] = $this->_encodeValue($element, $elementType);
            }
            return '[' . implode(', ', $elements) . ']';
        }
        if ($type->type == "map") {
            /**
             * @var MapType $type
             */
            $indexType = $type->indexType;
            $elementType = $type->elementType;
            if (!is_array($value)) {
                $value = array($value);
            }
            $elements = array();
            foreach ($value as $key => $element) {
                $elements[] = $this->_encodeKey($key, $indexType) . ': ' . $this->_encodeValue($element,
                        $elementType);
            }
            return '{' . implode(', ', $elements) . '}';
        }
        return "null";
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

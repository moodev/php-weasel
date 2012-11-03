<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

use Weasel\Common\Utils\ReflectionUtils;
use Weasel\JsonMarshaller\Exception\InvalidTypeException;

class JsonMapper
{

    /**
     * @var \Weasel\JsonMarshaller\Config\JsonConfigProvider The source of our configuration
     */
    protected $configProvider;

    /**
     * @var Types\Type[] Array mapping type names to their handlers
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
        $this->registerType("boolean", new Types\BoolType(), array("bool"));
        $this->registerType("float", new Types\FloatType());
        $this->registerType("integer", new Types\IntType(), array("int"));
        $this->registerType("string", new Types\StringType());
        $this->registerType("datetime", new Types\DateTimeType());
    }

    /**
     * Given a string of JSON, decode it into an instance of the named $class.
     * @param string $string JSON string containing an object
     * @param string $class Full namespaced name of the class this JSON represents.
     * @return mixed A populated instance of $class
     */
    public function readString($string, $class)
    {
        $decoded = json_decode($string, true);
        return $this->_decodeClass($decoded, $class);
    }

    /**
     * Given a string containing a JSON array decode it into an array of the named $class.
     * @param string $string JSON string containing an array
     * @param string $class Full namespaced name of the class this JSON array contains
     * @return array Array of populated $class instances
     */
    public function readArray($string, $class)
    {
        $response = array();
        $decoded = json_decode($string, true);
        foreach ($decoded as $object) {
            $response[] = $this->_decodeClass($object, $class);
        }
        return $response;
    }

    /**
     * Serialize an object to a string of JSON.
     * @param object $object Object to serialize
     * @return string The JSON
     */
    public function writeString($object)
    {
        return json_encode($this->_encodeObject($object), JSON_FORCE_OBJECT);
    }

    /**
     * Serialize an object to an array suitable for passing to json_encode.
     * @param object $object The object to serialize
     * @return array An array suitable for json_encode.
     */
    public function writeArray($object)
    {
        return $this->_encodeObject($object);
    }

    /**
     * Encode an object to an array representation based on our configuration.
     * @param object $object Object to serialize.
     * @param Config\Serialization\TypeInfo $typeInfo TypeInfo to override that from the class config, used when a
     * property has TypeInfo associated with it.
     * @throws Exception\InvalidTypeException
     * @throws \Exception
     * @return array
     */
    protected function _encodeObject($object, $typeInfo = null)
    {
        $class = get_class($object);
        $classconfig = $this->configProvider->getConfig($class);
        if (!isset($classconfig)) {
            throw new \Exception("No configuration found for class $class");
        }
        $config = $classconfig->serialization;

        if (!isset($typeInfo)) {
            $typeInfo = $config->typeInfo;
        }

        $result = array();
        if (isset($config->anyGetter)) {
            $method = $config->anyGetter;
            $result = $object->$method();
            if (!isset($result)) {
                $result = array();
            }
            if (!is_array($result)) {
                throw new InvalidTypeException("array", $result);
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

            $result[$key] = $this->_encodeValue($value, $propConfig->type, $propConfig->typeInfo);
            if (is_object($value) &&
                $propConfig->typeInfo &&
                $propConfig->typeInfo->typeInfoAs === Config\Serialization\TypeInfo::TI_AS_EXTERNAL_PROPERTY
            ) {
                // We need to store the type of the encoded object on ourselves
                $propClass = get_class($object);
                if (isset($typeInfo->subTypes[$propClass])) {
                    $classId = $typeInfo->subTypes[$propClass];

                    $result[$propConfig->typeInfo->typeInfoProperty] = $classId;
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
                    $result[$property] = $this->_encodeValue($classId, "string");
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY:
                    // We're actually going to encase this encoded object in an array containing the classId.
                    if (!isset($classId)) {
                        break;
                    }
                    $result = array($classId,
                                    $result
                    );
                    break;
                case Config\Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT:
                    // Very similar yo the wrapper array case, but this time it's a map from the classId to the object.
                    if (!isset($classId)) {
                        break;
                    }
                    $result = array($classId => $result);
                    break;
                default:
                    throw new \Exception("Unsupported type info storage at class level");
            }
        }

        return $result;

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
            $indexType = "int";
        }
        return array("array",
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
            case "array":
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
                 * @var $typeHandler Types\Type
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
            return null;
        }
        $typeData = $this->_parseType($type);
        switch (array_shift($typeData)) {
            case "complex":
                if (!is_object($value)) {
                    throw new InvalidTypeException($type, $value);
                }
                return $this->_encodeObject($value, $typeInfo);
                break;
            case "array":
                list ($indexType, $elementType) = $typeData;
                $result = array();
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($value as $key => $element) {
                    $result[$this->_encodeValue($key, $indexType)] = $this->_encodeValue($element, $elementType);
                }
                return $result;
                break;
            default:
                /**
                 * @var $typeHandler Types\Type
                 */
                list ($typeHandler) = $typeData;
                return $typeHandler->encodeValue($value, $this);

        }

    }


    /**
     * @param string $name
     * @param Types\Type $handler
     * @param string[] $aliases
     */
    public function registerType($name, $handler, $aliases = array())
    {
        $this->typeHandlers[$name] = $handler;
        foreach ($aliases as $alias) {
            $this->typeHandlers[$alias] = $handler;
        }
    }
}

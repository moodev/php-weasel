<?php
namespace PhpXmlMarshaller;

class XmlMapper
{

    /**
     * @var \PhpXmlMarshaller\Config\ConfigProvider
     */
    protected $configProvider;

    public function __construct(\PhpXmlMarshaller\Config\ConfigProvider $configProvider) {
        $this->configProvider = $configProvider;
    }

    public function readString($string, $class) {
        $reader = new \XMLReader();
        $reader->setParserProperty(\XMLReader::VALIDATE, true);
        $reader->xml($string);
        $ret = $this->readXml($reader, $class);
        $reader->close();
        return $ret;
    }

    public function readXml(\XmlReader $xml, $class) {
        $xml->read();

        return $this->_readObject($xml, $class, true);

        // TODO check end of file?
    }

    protected function _readObject(\XMLReader $xml, $class, $root = false) {
        if ($xml->isEmptyElement) {
            return null;
        }

        $deConfig = $this->configProvider->getConfig($class)->deserialization;
        // TODO handle simple value objecty things (XmlValue)

        if (!empty($deConfig->subClasses)) {
            if (!empty($deConfig->discriminator)) {
                if (substr($deConfig->discriminator, 0, 1) !== '@') {
                    throw new \Exception("Unsupported discriminator, currently only attributes (denoted with @) are supported. Found: " . $deConfig->discriminator);
                }

                $discrimValue = $xml->getAttribute(substr($deConfig->discriminator, 1));
                foreach ($deConfig->subClasses as $subClass) {
                    $subConfig = $this->configProvider->getConfig($subClass)->deserialization;
                    if ($subConfig->discriminatorValue == $discrimValue) {
                        $class = $subClass;
                        $deConfig = $subConfig;
                        break;
                    }
                }
            } elseif ($root) {
                $matchName = $xml->localName;
                $matchNS = $xml->namespaceURI;

                foreach ($deConfig->subClasses as $subClass) {
                    $subConfig = $this->configProvider->getConfig($subClass)->deserialization;
                    if ($root) {
                        if ($subConfig->name == $matchName && $subConfig->namespace == $matchNS) {
                            $class = $subClass;
                            $deConfig = $subConfig;
                            break;
                        }
                    }
                }
            }

        }

        if ($root && ($xml->localName != $deConfig->name || $xml->namespaceURI != $deConfig->namespace)) {
            throw new \Exception("Unable to resolve root node for {$xml->namespaceURI}:{$xml->localName}");
        }

        $creatorClass = $class;
        if (!empty($deConfig->factoryClass)) {
            $creatorClass = $deConfig->factoryClass;
        }

        if (!empty($deConfig->factoryMethod)) {
            $creatorMethod = $deConfig->factoryMethod;
            $object = $creatorClass::$creatorMethod();
        } else {
            $object = new $class();
        }

        $seenAttributes = array();
        $seenElements = array();


		if ($xml->hasAttributes) {
            while ($xml->moveToNextAttribute()) {
                $seenAttributes[] = $xml->name;
                if (!isset($deConfig->attributes[$xml->namespaceURI][$xml->localName])) {
                    throw new \Exception("Unknown attribute found in {$class}: {$xml->name}");
                }
                $val = $this->_readAttribute($xml, $deConfig->attributes[$xml->name]);
                $this->_setProperty($object, $deConfig->attributes[$xml->name]->property, $val);
                break;
            }
            $xml->moveToElement();
        }

        $knownArrays = array();
        do {
            if (!$xml->read()) {
                throw new \Exception("XML Read failure parsing for {$class}");
            }
            switch ($xml->nodeType) {
                case \XMLReader::ELEMENT:
                    if (isset($deConfig->elementWrappers[$xml->name])) {
                        // TODO
                        break;
                    }
                    if (!isset($deConfig->elements[$xml->name])) {
                        throw new \Exception("Unknown attribute found in {$class}: {$xml->name}");
                    }
                    $seenElements[] = $xml->name;
                    $val = $this->_readElement($xml, $deConfig->elements[$xml->name]);
                    if (is_array($val)) {
                        if (!isset($knownArrays[$xml->name])) {
                            $knownArrays[$xml->name] = array();
                        }
                        $knownArrays[$xml->name] = array_merge($knownArrays[$xml->name], $val);
                    } else {
                        $this->_setProperty($object, $deConfig->attributes[$xml->name]->property, $val);
                    }

                    break;
                case \XMLReader::WHITESPACE:
                case \XMLReader::COMMENT:
                    break;
                default:
                    throw new \Exception("XML Parsing error, found unexpected {$xml->nodeType} while parsing for {$class}");

            }
        } while ($xml->nodeType != \XMLReader::END_ELEMENT);

        foreach ($knownArrays as $name => $val) {
            $this->_setProperty($object, $deConfig->attributes[$name]->property, $val);
        }

        $notSeenAtts = array_diff($deConfig->requiredAttributes, $seenAttributes);
        $notSeenElements = array_diff($deConfig->requiredElements, $seenElements);
        if (!empty($notSeenAtts) || !empty($notSeenElements)) {
            throw new \Exception("Missing required elements: ".implode(', ', $notSeenElements)." and attributes: " . implode(',', $notSeenAtts) . "on $class");
        }

        return $object;

    }

    protected function _setProperty($object, Config\Deserialization\PropertyDeserialization $propConfig, $value) {
        if ($propConfig instanceof Config\Deserialization\DirectDeserialization) {
            /**
             * @var Config\Deserialization\DirectDeserialization $propConfig
             */

            $prop = $propConfig->property;
            $object->$prop = $value;
        } elseif ($propConfig instanceof Config\Deserialization\SetterDeserialization) {
            /**
             * @var Config\Deserialization\SetterDeserialization $propConfig
             */

            $meth = $propConfig->method;
            $object->$meth($value);
        }
    }

    protected function _readElement(\XMLReader $xml, Config\Deserialization\ElementDeserialization $config) {
        if ($xml->isEmptyElement && !$config->nillable) {
            throw new \Exception("Found empty {$xml->name} which is not nillable");
        }

        return $this->_readElementAsType($xml, $config->property->type);

    }

    /**
     * @param \XMLReader $xml
     * @param string $type
     * @return mixed
     */
    protected function _readElementAsType($xml, $type) {
        $matches = array();
        $ret = null;
        if (!preg_match('/^(.*)\\[(int|integer|string|bool|boolean|float|)\\]$/i', $type, $matches)) {
            switch ($type) {
                case "bool":
                case "boolean":
                case "int":
                case "integer":
                case "float":
                case "string":
                    $ret = $this->_decodeSimpleValue($xml->value, $type);
                    break;
                default:
                    // Object! (hopefully)
                    $ret = $this->_readObject($xml, $type, false);
            }
        } else {
            // It's an array
            $elementType = $matches[1];

            $indexType = $matches[2];

            // We return an array of the element(s) we've read.
            // This will be merged if necessary by whoever we return it to.
            $result = array();

            if (empty($indexType)) {
                // It's an array element, not a map.
                $ret = array($this->_readElementAsType($xml, $elementType));
            } else {
                $this->_readMap($xml, $indexType, $elementType);
            }

        }

        $xml->next();
        return $ret;
    }

    protected function _readMap(\XMLReader $xml, $indexType, $elementType) {

        $array = array();

        $inEntry = false;

        $key = null;
        $value = null;

        do {
            if (!$xml->read()) {
                throw new \Exception("XML Read failure parsing array");
            }
            switch ($xml->nodeType) {
                case \XMLReader::ELEMENT:
                    if (!$inEntry && $xml->localName !== 'entry') {
                        throw new \Exception("Expected map entry, got: " . $xml->name);
                    }
                    if ($xml->localName === 'entry') {
                        $inEntry = true;
                    } elseif ($xml->localName === 'key') {
                        if (isset($key)) {
                            throw new \Exception("Found multiple keys for entry");
                        }
                        $key = $this->_readElementAsType($xml, $indexType);
                    } elseif ($xml->localName === 'value') {
                        if (isset($key)) {
                            throw new \Exception("Found multiple values for entry");
                        }
                        $key = $this->_readElementAsType($xml, $elementType);
                    } else {
                        throw new \Exception("Found unexpected node {$xml->name} when reading map");
                    }
                    break;
                case \XMLReader::END_ELEMENT:
                    if ($xml->localName === 'entry') {
                        $xml->next();
                        $inEntry = false;
                    }
                    break;
                case \XMLReader::WHITESPACE:
                case \XMLReader::COMMENT:
                    break;
                default:
                    throw new \Exception("XML Parsing error, found unexpected {$xml->nodeType}");

            }
        } while ($xml->nodeType != \XMLReader::END_ELEMENT);

        return $array;
    }

    protected function _readAttribute(\XMLReader $xml, Config\Deserialization\AttributeDeserialization $config) {
        return $this->_decodeSimpleValue($xml->value, $config->property->type);
    }

    public function writeString($object) {

    }

    protected function _decodeSimpleValue($value, $type) {
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
                throw new \Exception("Invalid simple type: " . $type);
        }

    }

    /**
     * @param mixed $value
     * @param string $type
     * @throws \Exception
     * @param Config\Serialization\TypeInfo $typeInfo
     * @return mixed
     */
    /*
    protected function _encodeValue($value, $type, $typeInfo = null) {
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
                    return $this->_encodeObject($value, $typeInfo);
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

    }   */
}

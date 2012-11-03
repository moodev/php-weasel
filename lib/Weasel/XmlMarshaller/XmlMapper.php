<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\XmlMarshaller;

class XmlMapper
{

    /**
     * @var \Weasel\XmlMarshaller\Config\ConfigProvider
     */
    protected $configProvider;

    public function __construct(\Weasel\XmlMarshaller\Config\ConfigProvider $configProvider)
    {
        $this->configProvider = $configProvider;
    }

    public function readString($string, $class)
    {
        $reader = new \XMLReader();
//        $reader->setParserProperty(\XMLReader::VALIDATE, true);
        $reader->xml($string);
        $ret = $this->readXml($reader, $class);
        $reader->close();
        return $ret;
    }

    public function readXml(\XmlReader $xml, $class)
    {
        $xml->read();

        return $this->_readObject($xml, $class, true);

        // TODO check end of file?
    }

    protected function _readObject(\XMLReader $xml, $class, $root = false)
    {
        $deConfig = $this->configProvider->getConfig($class)->deserialization;
        if (!isset($deConfig)) {
            throw new \Exception("No config count for $class");
        }
        // TODO handle simple value objecty things (XmlValue)

        $ignorableAttributes = array();

        if (!empty($deConfig->subClasses)) {
            if (!empty($deConfig->discriminator)) {
                if (substr($deConfig->discriminator, 0, 1) !== '@') {
                    throw new \Exception(
                        "Unsupported discriminator, currently only attributes (denoted with @) are supported. Found: " .
                            $deConfig->discriminator);
                }
                $discrimName = substr($deConfig->discriminator, 1);
                $attrNS = ($xml->namespaceURI) ? $xml->namespaceURI . ':' : '';
                $ignorableAttributes[$attrNS . $discrimName] = true;

                $discrimValue = $xml->getAttribute($discrimName);
                foreach ($deConfig->subClasses as $subClass) {
                    $subConfig = $this->configProvider->getConfig($subClass)->deserialization;
                    if ($subConfig->discriminatorValue == $discrimValue) {
                        $class = $subClass;
                        $deConfig = $subConfig;
                        break;
                    }
                }
            } elseif ($root && ($xml->name != $deConfig->name || $xml->namespaceURI != $deConfig->namespace)) {
                $matchName = $xml->name;
                $matchNS = $xml->namespaceURI;

                foreach ($deConfig->subClasses as $subClass) {
                    $subConfig = $this->configProvider->getConfig($subClass)->deserialization;
                    if ($subConfig->name == $matchName && $subConfig->namespace == $matchNS) {
                        $class = $subClass;
                        $deConfig = $subConfig;
                        break;
                    }
                }
            }

        }

        $fullName = $xml->namespaceURI . ':' . $xml->name;

        if ($root && ($xml->name != $deConfig->name || $xml->namespaceURI != $deConfig->namespace)) {
            throw new \Exception("Unable to resolve root node for {$fullName}");
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

        $namespace = $xml->namespaceURI;

        if ($xml->hasAttributes) {
            while ($xml->moveToNextAttribute()) {
                $attrNS =
                    (($xml->namespaceURI) ? $xml->namespaceURI . ':' : (isset($namespace) ? $namespace . ':' : ''));
                $fullName = $attrNS . $xml->name;
                if ($xml->name === 'xmlns') {
                    continue;
                }
                if (isset($ignorableAttributes[$fullName])) {
                    continue;
                }
                if (!isset($deConfig->attributes[$fullName])) {
                    throw new \Exception("Unknown attribute found in {$class}: {$fullName}");
                }
                $val = $this->_readAttribute($xml, $deConfig->attributes[$fullName]);
                $this->_setProperty($object, $deConfig->attributes[$fullName]->property, $val);
                $seenAttributes[] = $deConfig->attributes[$fullName]->property->id;
            }
            $xml->moveToElement();
        }

        $knownValues = array();

        if (!$xml->isEmptyElement) {
            while (true) {
                if (!$xml->read()) {
                    throw new \Exception("XML read error");
                }
                switch ($xml->nodeType) {
                    case \XMLReader::ELEMENT:
                        list($propType, $val) = $this->_readElement($xml, $deConfig);
                        $seenElements[] = $propType->id;
                        if (is_array($val)) {
                            if (!isset($knownValues[$propType->id])) {
                                $knownValues[$propType->id] =
                                    array($propType,
                                          array()
                                    );
                            }
                            $knownValues[$propType->id][1] = array_merge($knownValues[$propType->id][1], $val);
                        } else {
                            $this->_setProperty($object, $propType, $val);
                        }
                        break;
                    case \XMLReader::END_ELEMENT:
                        break 2;
                    case \XMLReader::WHITESPACE:
                    case \XMLReader::COMMENT:
                    case \XMLReader::SIGNIFICANT_WHITESPACE:
                        break;
                    default:
                        throw new \Exception("XML Parsing error, found unexpected {$xml->nodeType} while parsing for {$class}");
                }
            }
        }

        $notSeenAtts = array_diff($deConfig->requiredAttributes, $seenAttributes);
        $notSeenElements = array_diff($deConfig->requiredElements, $seenElements);
        if (!empty($notSeenAtts) || !empty($notSeenElements)) {
            // TODO fix.
            throw new \Exception("Missing required elements: " . implode(', ', $notSeenElements) . " and attributes: " .
                                     implode(',', $notSeenAtts) . "on $class");
        }

        foreach ($knownValues as $typeval) {
            list ($property, $values) = $typeval;
            $this->_setProperty($object, $property, $values);
        }

        return $object;

    }

    protected function _setProperty($object, Config\Deserialization\PropertyDeserialization $propConfig, $value)
    {
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

    protected function _readElementWrapper(\XMLReader $xml, Config\Deserialization\ElementWrapper $wrapperConfig)
    {

        $elementConfig = $wrapperConfig->wraps;

        $collection = array();

        while (true) {
            if (!$xml->read()) {
                throw new \Exception("XML Read failure parsing wrapper");
            }
            $fullName = $xml->namespaceURI . ':' . $xml->name;
            switch ($xml->nodeType) {
                case \XMLReader::ELEMENT:
                    if ($elementConfig->ref) {
                        $type = $this->_getRef($elementConfig, $fullName);
                    } else {
                        $type = $elementConfig->property->type;
                    }
                    if (empty($type)) {
                        throw new \Exception(
                            "Unable to resolve wrapped type for " . $wrapperConfig->name . " base type " .
                                $wrapperConfig->wraps->property->type . " looking for " . $fullName);
                    }
                    $collection[] = $this->_readElementAsType($xml, $type);
                    break;
                case \XMLReader::END_ELEMENT:
                    break 2;
                case \XMLReader::WHITESPACE:
                case \XMLReader::SIGNIFICANT_WHITESPACE:
                case \XMLReader::COMMENT:
                    break;
                default:
                    throw new \Exception("XML Parsing error, found unexpected {$xml->nodeType}");

            }
        }

        return array($elementConfig->property,
                     $collection
        );
    }

    /**
     * @param \Weasel\XmlMarshaller\Config\Deserialization\ElementDeserialization $ref
     * @param string $fullName
     * @return string the type
     */
    protected function _getRef(Config\Deserialization\ElementDeserialization $ref, $fullName)
    {
        if (isset($ref->refNameToTypeMap)) {
            if (isset($ref->refNameToTypeMap[$fullName])) {
                return $ref->refNameToTypeMap[$fullName];
            } else {
                return null;
            }
        }
        $superConfig = $this->configProvider->getConfig($ref->property->type)->deserialization;
        $superFullName = (isset($superConfig->namespace) ? $superConfig->namespace . ":" : "") . $superConfig->name;
        $ref->refNameToTypeMap[$superFullName] = $ref->property->type;
        foreach ($superConfig->subClasses as $subClass) {
            $subConfig = $this->configProvider->getConfig($subClass)->deserialization;
            $subFullName = (isset($subConfig->namespace) ? $subConfig->namespace . ":" : "") . $subConfig->name;
            $ref->refNameToTypeMap[$subFullName] = $subClass;
        }
        if (isset($ref->refNameToTypeMap[$fullName])) {
            return $ref->refNameToTypeMap[$fullName];
        }
        return null;
    }

    protected function _readElement(\XMLReader $xml, Config\Deserialization\ClassDeserialization $deConfig)
    {
        $fullName = $xml->namespaceURI . ':' . $xml->name;
        if (isset($deConfig->elementWrappers[$fullName])) {
            return $this->_readElementWrapper($xml, $deConfig->elementWrappers[$fullName], $deConfig);
        }

        $type = null;
        $element = null;

        foreach ($deConfig->elements as $el) {
            if (!$el->ref && $el->name == $xml->name && $el->namespace == $xml->namespaceURI) {
                $element = $el;
                $type = $el->property->type;
                break;
            }
        }
        if (!isset($element)) {
            foreach ($deConfig->elements as $el) {
                if ($el->ref) {
                    if ($type = $this->_getRef($el, $fullName)) {
                        $element = $el;
                        break;
                    }
                }
            }
        }
        if (!isset($element)) {
            throw new \Exception("Unknown element found in {$deConfig->name}: {$fullName}");
        }

        return array($element->property,
                     $this->_readElementAsType($xml, $type)
        );
    }

    /**
     * @param \XMLReader $xml
     * @param string $type
     * @param bool $root
     * @return mixed
     */
    protected function _readElementAsType($xml, $type, $root = false)
    {
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
                    $ret = $this->_decodeSimpleValue($xml->readInnerXml(), $type);
                    if (!$xml->isEmptyElement) {
                        $open = 1;
                        while ($open > 0) {
                            if (!$xml->read()) {
                                throw new \Exception("XML Read failure parsing simple value");
                            }
                            if ($xml->nodeType == \XMLReader::ELEMENT && !$xml->isEmptyElement) {
                                $open++;
                            } elseif ($xml->nodeType == \XMLReader::END_ELEMENT) {
                                $open--;
                            }
                        }
                    }
                    break;
                default:
                    // Object! (hopefully)
                    $ret = $this->_readObject($xml, $type, $root);
            }
        } else {
            // It's an array
            $elementType = $matches[1];

            $indexType = $matches[2];

            // We return an array of the element(s) we've read.
            // This will be merged if necessary by whoever we return it to.

            if (empty($indexType)) {
                // It's an array element, not a map.
                $ret = array($this->_readElementAsType($xml, $elementType));
            } else {
                $ret = $this->_readMap($xml, $indexType, $elementType);
            }

        }

        return $ret;
    }

    protected function _readMap(\XMLReader $xml, $indexType, $elementType)
    {

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
                    if (!$inEntry && $xml->name !== 'entry') {
                        throw new \Exception("Expected map entry, got: " . $xml->name);
                    }
                    if ($xml->name === 'entry') {
                        $inEntry = true;
                    } elseif ($xml->name === 'key') {
                        if (isset($key)) {
                            throw new \Exception("Found multiple keys for entry");
                        }
                        $key = $this->_readElementAsType($xml, $indexType);
                    } elseif ($xml->name === 'value') {
                        if (isset($key)) {
                            throw new \Exception("Found multiple values for entry");
                        }
                        $key = $this->_readElementAsType($xml, $elementType);
                    } else {
                        throw new \Exception("Found unexpected node {$xml->name} when reading map");
                    }
                    break;
                case \XMLReader::END_ELEMENT:
                    if ($xml->name === 'entry') {
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

    protected function _readAttribute(\XMLReader $xml, Config\Deserialization\AttributeDeserialization $config)
    {
        return $this->_decodeSimpleValue($xml->value, $config->property->type);
    }

    public function writeString($object, $class = null)
    {
        throw new \Exception("Not implemented yet");
    }

    protected function _decodeSimpleValue($value, $type)
    {
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

}

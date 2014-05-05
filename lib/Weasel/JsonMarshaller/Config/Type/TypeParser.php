<?php


namespace Weasel\JsonMarshaller\Config\Type;


class TypeParser
{

    public static function parseTypeString($type)
    {
        // Assume type strings are well formed: look for the last [ to see if it's an array or map.
        // Note that this might be an array of arrays, and we're after the outermost type, so we're after the last [!
        $pos = strrpos($type, '[');
        if ($pos === false) {
            // If there wasn't a [ then this must be a scalar or object
            if ($type == "bool") {
                $type = "boolean";
            }
            if ($type == "int") {
                $type = "integer";
            }
            return new ScalarType($type);
        }

        // Extract the base type, and whatever's between the [...] as the index type.
        // Potentially the type string is actually badly formed:
        // e.g. this code will accept string[int! as being an array of string with index int.
        // Bah. I'll ignore that case for now. This bit of code gets called a lot, I'd rather not add another substr.
        $elementType = substr($type, 0, $pos);
        $indexType = substr($type, $pos + 1, -1);

        $elementType = self::parseTypeString($elementType);

        if ($indexType === "") {
            // The [...] were empty. It's an array.
            return new ListType($elementType);
        }

        $indexType = self::parseTypeString($indexType);

        // Must be a map then.
        return new MapType($indexType, $elementType);
    }
}
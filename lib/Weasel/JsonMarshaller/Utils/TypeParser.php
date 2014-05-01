<?php
namespace Weasel\JsonMarshaller\Utils;

use Weasel\JsonMarshaller\Config\Type\ListType;
use Weasel\JsonMarshaller\Config\Type\MapType;
use Weasel\JsonMarshaller\Config\Type\ScalarType;

class TypeParser {

    public static function parseType($type, $recurse = false)
    {
        // Assume type strings are well formed: look for the last [ to see if it's an array or map.
        // Note that this might be an array of arrays, and we're after the outermost type, so we're after the last [!
        $pos = strrpos($type, '[');
        if ($pos === false) {
            // If there wasn't a [ then it's not an array of any sort.
            return new ScalarType($type); // Could also be an "object", but we just don't know right now.
        }

        // Extract the base type, and whatever is between the [...] as the index type.
        // Potentially the type string is actually badly formed:
        // e.g. this code will accept string[int! as being an array of string with index int.
        // Bah. I'll ignore that case for now. This bit of code gets called a lot, I'd rather not add another substr.
        $elementType = substr($type, 0, $pos);
        $indexType = substr($type, $pos + 1, -1);

        if ($recurse) {
            // If we're recursing then try to parse the element type too
            $elementType = self::parseType($elementType, true);
        }
        if ($indexType === "") {
            // The [...] were empty. It's an array.
            return new ListType($elementType);
        }
        return new MapType(new ScalarType($indexType), $elementType);
    }

} 
PHP Weasel
==========
[![Build Status](https://travis-ci.org/moodev/php-weasel.png)](https://travis-ci.org/moodev/php-weasel)

Weasel is an object marshalling library for PHP supporting JSON and XML. Marshalling is, by default, configured using “annotations”, driven by the `Doctrine\Common\Annotation` library.

It also includes its own Annotation library, `Weasel\Annotation`, but this is considered deprecated in favour of using Doctrine.

The latest version can be found here: https://github.com/moodev/php-weasel

The documentation can be found here: https://github.com/moodev/php-weasel/wiki

Installation
------------

If you can, use [composer](http://getcomposer.org/). If you can't, you'll find the list of dependencies in [composer.json](./composer.json).

Usage
-----

Annotate the classes you want to serialize/deserialize (see the documentation.)

Then:
```php
$factory = new WeaselDoctrineAnnotationDrivenFactory();
// optionally:
    $factory->setCache($cacheInstance);
    $factory->setLogger($psr3LoggerInstance);

$mapper = $factory->getJsonMapperInstance();
$thing = $mapper->readString($json, $typeToDecodeTo);
$backToJson = $mapper->writeString($thing);
```
Why?
----
`json_decode()` decodes to arrays or stdObj. Weasel, while making use of `json_decode()` internally, maps to objects; it's basically a configuration driven Object Mapper for JSON.

Meanwhile, `json_encode()` gives you no fine grained control over how your data gets serialized to JSON. You can't disable marshalling of fields, you can't add typing information without adding it to your classes, and, most annoyingly, you don't have any control over how `array()` gets mapped on a field-by-field basis. Weasel allows you to configure how fields are marshalled on a per-field level: if you've got an array that should always be mapped as a JSON object, while other arrays need to be mapped as an array, you can have just that.

The `XmlMarshaller` just seemed like a good idea at the time. It probably wasn't. It's incomplete and unloved. As soon as I find a better approach to the problem, it'll be deprecated.

This project spawned from work on [moo-php](https://github.com/JonathanO/moo-php), a client library for the moo.com API. After writing serialization/deserialization code that was specific to that object structure, I realised that a general purpose, configurable marshaller, similar to Jackson in the Java world, would be rather useful. Weasel is the result.

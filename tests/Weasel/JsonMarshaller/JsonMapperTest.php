<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

use PHPUnit\Framework\TestCase;
use Weasel\JsonMarshaller\Config\JsonConfigProvider;

class JsonMapperTest extends TestCase
{


    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringBasicObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(array(
                    "blah" => "foo"
                )
            ),
            $mtc
        );

        $this->assertInstanceOf($mtc, $result);
        $this->assertEquals(new MockTestClass("foo"), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testChangeStrictDefault()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider, false);

        $result = $mapper->readString(json_encode(array(
                    "blah" => 1.0
                )
            ),
            $mtc
        );

        $this->assertInstanceOf($mtc, $result);
        $this->assertEquals(new MockTestClass("1"), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringNull()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(null),
            $mtc
        );
        $this->assertEquals(null, $result);

        $result = $mapper->readString("",
            $mtc
        );

        $this->assertEquals(null, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode("foo"), "string");

        $this->assertInternalType("string", $result);
        $this->assertEquals("foo", $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringArrayPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(array("foo", "bar", "baz")), "string[]");

        $this->assertInternalType("array", $result);
        $this->assertEquals(array("foo", "bar", "baz"), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringMapPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(array("a" => 123, "b" => 34, "c" => 99)), "int[string]");

        $this->assertInternalType("array", $result);
        $this->assertEquals(array("a" => 123, "b" => 34, "c" => 99), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringMapIntKeys()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString('{"77": 123, "33": 34, "22": 99}', "int[int]");

        $this->assertInternalType("array", $result);
        $this->assertEquals(array(77 => 123, 33 => 34, 22 => 99), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadStringArrayOfBasicObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(
                array(
                    array(
                        "blah" => "foo"
                    ),
                    array(
                        "blah" => "bar"
                    ),
                    array(
                        "blah" => "baz"
                    ),
                )
            ),
            $mtc . '[]'
        );

        $this->assertInternalType("array", $result);
        $this->assertEquals(array(new MockTestClass("foo"), new MockTestClass("bar"), new MockTestClass("baz")),
            $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringBasicObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeArray(new MockTestClass("foo"));

        $this->assertEquals(array("blah" => "foo"), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeArray("blah");

        $this->assertEquals("blah", $result);
    }


    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringArrayPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeString(array("foo", "bar", "baz"), 'string[]');

        $this->assertEquals('["foo", "bar", "baz"]', $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringMapPrimitive()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeArray(array("a" => 123, "b" => 34, "c" => 99), 'int[string]');

        $this->assertInternalType("array", $result);
        $this->assertEquals(array("a" => 123, "b" => 34, "c" => 99), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringMapIntKeys()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeString(array(77 => 123, 42 => 34, 99 => 99), 'int[int]');

        $expected = '{"77": 123, "42": 34, "99": 99}';

        $this->assertEquals($expected, $result);
    }

    /**
     * Test to show that something that PHP would normally think is an "array" gets encoded as a map when we ask for that.
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringMapIntKeysLooksLikeArray()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeString(array(123, 34, 99), 'int[int]');

        $expected = '{"0": 123, "1": 34, "2": 99}';

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testWriteStringArrayOfBasicObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->writeArray(
            array(
                new MockTestClass("foo"),
                new MockTestClass("bar"),
                new MockTestClass("baz"),
            ),
            $mtc . '[]'
        );

        $this->assertEquals(array(array("blah" => "foo"), array("blah" => "bar"), array("blah" => "baz")), $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testReadArrayOfBasicObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "blah", "string");
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readArray(json_encode(
                array(
                    array(
                        "blah" => "foo"
                    ),
                    array(
                        "blah" => "bar"
                    ),
                    array(
                        "blah" => "baz"
                    ),
                )
            ),
            $mtc
        );

        $this->assertInternalType("array", $result);
        $this->assertEquals(array(new MockTestClass("foo"), new MockTestClass("bar"), new MockTestClass("baz")),
            $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testAnySetter()
    {


        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $config->deserialization = new Config\Deserialization\ClassDeserialization();
        $config->deserialization->anySetter = "anySetter";

        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $result = $mapper->readString(json_encode(array(
                    "cows" => "blork",
                )
            ),
            $mtc
        );

        $this->assertInstanceOf($mtc, $result);
        $this->assertEquals(array("cows" => "blork"), $result->any);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testAnyGetter()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $config->serialization = new Config\Serialization\ClassSerialization();
        $config->serialization->anyGetter = "anyGetter";

        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $object = new MockTestClass();
        $object->any = array("cows" => "blork");

        $result = $mapper->writeArray($object);
        $this->assertInternalType("array", $result);
        $this->assertEquals(array("cows" => "blork"), $result);
    }

    protected function addPropConfig(Config\ClassMarshaller $config, $name, $type)
    {
        $prop = new Config\Serialization\DirectSerialization();
        $prop->type = $type;
        $prop->property = $name;
        $config->serialization->properties[$name] = $prop;

        $prop = new Config\Deserialization\DirectDeserialization();
        $prop->type = $type;
        $prop->property = $name;
        $config->deserialization->properties[$name] = $prop;

        return $config;
    }

    /**
     * Build a handy configuration for testing object inheritance using TypeInfo.
     *
     * @param string $typeInfoAs A string containing the name of one of the TypeInfo TI_AS_ constants.
     * @param string $typeInfoUse A string containing the name of one of the TypeInfo TI_USE_ constants.
     * @return MockedConfigProvider A config provider with a useful config for testing TypeInfo.
     */
    protected function buildTypeInfoTestConfig($typeInfoAs, $typeInfoUse = "TI_USE_CLASS")
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';
        $mtcb = 'Weasel\JsonMarshaller\MockTestClassB';
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';
        $mtcd = 'Weasel\JsonMarshaller\MockTestClassD';

        $config = new Config\ClassMarshaller();
        $config->serialization->typeInfo = new Config\Serialization\TypeInfo();
        $config->serialization->typeInfo->typeInfoAs = constant('\Weasel\JsonMarshaller\Config\Serialization\TypeInfo::' . $typeInfoAs);
        $config->serialization->typeInfo->typeInfo = constant('\Weasel\JsonMarshaller\Config\Serialization\TypeInfo::' . $typeInfoUse);
        $config->serialization->typeInfo->typeInfoProperty = "type";
        $config->serialization->typeInfo->subTypes[$mtcc] = $mtcc;
        $config->serialization->typeInfo->subTypes[$mtcd] = $mtcd;
        $config->deserialization->typeInfo = new Config\Deserialization\TypeInfo();
        $config->deserialization->typeInfo->typeInfoAs = constant('\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo::' . $typeInfoAs);
        $config->deserialization->typeInfo->typeInfo = constant('\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo::' . $typeInfoUse);
        $config->deserialization->typeInfo->typeInfoProperty = "type";
        $config->deserialization->typeInfo->subTypes[$mtcc] = $mtcc;
        $config->deserialization->typeInfo->subTypes[$mtcd] = $mtcd;
        $configProvider->fakeConfig[$mtcb] = $config;

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "hi", "string");
        $configProvider->fakeConfig[$mtcc] = $config;

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "bi", "string");
        $configProvider->fakeConfig[$mtcd] = $config;

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, 'blah', $mtcb);
        $configProvider->fakeConfig[$mtc] = $config;

        return $configProvider;

    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testEncodeWrapperObject()
    {
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_WRAPPER_OBJECT'));

        $object = new MockTestClass();
        $object->blah = new MockTestClassC("dog");

        $expected = array(
            "blah" =>
                array($mtcc => array("hi" => "dog"))
        );
        $result = $mapper->writeArray($object);
        $this->assertInternalType("array", $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testEncodeWrapperArray()
    {
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_WRAPPER_ARRAY'));

        $object = new MockTestClass();
        $object->blah = new MockTestClassC("dog");

        $expected = array(
            "blah" => array(
                $mtcc,
                array("hi" => "dog")
            )
        );
        $result = $mapper->writeArray($object);
        $this->assertInternalType("array", $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testEncodeTypeinfoAsPropertyInArray()
    {
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';
        $mtcd = 'Weasel\JsonMarshaller\MockTestClassD';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_PROPERTY'));

        $input = array();
        $arr = array();
        $object = new MockTestClass();
        $object->blah = new MockTestClassC("dog");
        $arr[] = $object;
        $object = new MockTestClass();
        $object->blah = new MockTestClassD("car");
        $arr[] = $object;
        $input[] = $arr;
        $arr = array();
        $object = new MockTestClass();
        $object->blah = new MockTestClassD("wobble");
        $arr[] = $object;
        $input[] = $arr;
        $input[] = array();

        $expected = array(
            array(
                array(
                    "blah" => array(
                        "hi" => "dog",
                        "type" => $mtcc,
                    )
                ),
                array(
                    "blah" => array(
                        "bi" => "car",
                        "type" => $mtcd,
                    )
                )
            ),
            array(
                array(
                    "blah" => array(
                        "bi" => "wobble",
                        "type" => $mtcd,
                    )
                ),
            ),
            array()
        );
        $result = $mapper->writeArray($input, $mtcc . '[][]');
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testEncodeTypeinfoAsProperty()
    {
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_PROPERTY'));

        $object = new MockTestClass();
        $object->blah = new MockTestClassC("dog");

        $expected = array(
            "blah" => array(
                "type" => $mtcc,
                "hi" => "dog"
            )
        );
        $result = $mapper->writeArray($object);
        $this->assertInternalType("array", $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testDecodeTypeinfoAsProperty()
    {
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_PROPERTY'));

        $json = json_encode(array(
            "blah" => array(
                "type" => $mtcc,
                "hi" => "dog"
            )
        ));

        $expected = new MockTestClass();
        $expected->blah = new MockTestClassC("dog");

        $result = $mapper->readString($json, $mtc);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testDecodeTypeinfoAsWrapperObject()
    {
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_WRAPPER_OBJECT'));

        $json = json_encode(array(
            "blah" => array(
                "$mtcc" => array("hi" => "dog")
            )
        ));

        $expected = new MockTestClass();
        $expected->blah = new MockTestClassC("dog");

        $result = $mapper->readString($json, $mtc);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testDecodeTypeinfoAsWrapperArray()
    {
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';
        $mtcc = 'Weasel\JsonMarshaller\MockTestClassC';

        $mapper = new JsonMapper($this->buildTypeInfoTestConfig('TI_AS_WRAPPER_ARRAY'));

        $json = json_encode(array(
            "blah" => array(
                $mtcc,
                array("hi" => "dog")
            )
        ));

        $expected = new MockTestClass();
        $expected->blah = new MockTestClassC("dog");

        $result = $mapper->readString($json, $mtc);
        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     * @expectedException \Weasel\JsonMarshaller\Exception\BadConfigurationException
     * @expectedExceptionMessage is not an instantiable class
     */
    public function testBadSubtypeOfInterface()
    {
        $configProvider = new MockedConfigProvider();
        $mti = 'Weasel\JsonMarshaller\MockTestInterface';

        $config = new Config\ClassMarshaller();

        $configProvider->fakeConfig[$mti] = $config;

        $mapper = new JsonMapper($configProvider);

        $mapper->readString(json_encode(array(
                    "blah" => "foo"
                )
            ),
            $mti
        );

    }

}

interface MockTestInterface
{

}

class MockTestClass implements MockTestInterface
{
    public $any = array();

    public $blah;

    public function anyGetter()
    {
        return $this->any;
    }

    public function anySetter($key, $value)
    {
        $this->any[$key] = $value;
    }

    public function __construct($blah = null)
    {
        $this->blah = $blah;
    }
}

class MockTestClassB
{
}

class MockTestClassC extends MockTestClassB
{
    public $hi;

    public function __construct($hi = "cat")
    {
        $this->hi = $hi;
    }

}

class MockTestClassD extends MockTestClassB
{
    public $bi;

    public function __construct($bi = "stoat")
    {
        $this->bi = $bi;
    }

}

class MockedConfigProvider implements JsonConfigProvider
{

    /**
     * @var Config\ClassMarshaller[]
     */
    public $fakeConfig = array();

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
     * @throws \ReflectionException
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller The config, or null if not found
     */
    public function getConfig($class)
    {
        if (isset($this->fakeConfig[$class])) {
            return $this->fakeConfig[$class];
        }
        return null;

    }

    public function setCache($cache)
    {
        // TODO: Implement setCache() method.
    }
}
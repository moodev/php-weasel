<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

use Weasel\JsonMarshaller\Config\JsonConfigProvider;

require_once(__DIR__ . '/../../../lib/WeaselAutoloader.php');

class JsonMapperTest extends \PHPUnit_Framework_TestCase
{


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

        $config = new Config\ClassMarshaller();
        $config->serialization->typeInfo = new Config\Serialization\TypeInfo();
        $config->serialization->typeInfo->typeInfoAs = constant('\Weasel\JsonMarshaller\Config\Serialization\TypeInfo::' . $typeInfoAs);
        $config->serialization->typeInfo->typeInfo = constant('\Weasel\JsonMarshaller\Config\Serialization\TypeInfo::' . $typeInfoUse);
        $config->serialization->typeInfo->typeInfoProperty = "type";
        $config->serialization->typeInfo->subTypes[$mtcc] = $mtcc;
        $config->deserialization->typeInfo = new Config\Deserialization\TypeInfo();
        $config->deserialization->typeInfo->typeInfoAs = constant('\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo::' . $typeInfoAs);
        $config->deserialization->typeInfo->typeInfo = constant('\Weasel\JsonMarshaller\Config\Deserialization\TypeInfo::' . $typeInfoUse);
        $config->deserialization->typeInfo->typeInfoProperty = "type";
        $config->deserialization->typeInfo->subTypes[$mtcc] = $mtcc;
        $configProvider->fakeConfig[$mtcb] = $config;

        $config = new Config\ClassMarshaller();
        $this->addPropConfig($config, "hi", "string");
        $configProvider->fakeConfig[$mtcc] = $config;

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
            $mtcc => array("hi" => "dog")
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
}

class MockTestClass
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

class MockedConfigProvider implements JsonConfigProvider
{

    /**
     * @var Config\ClassMarshaller[]
     */
    public $fakeConfig = array();

    /**
     * Obtain the config for a named class
     * @param string $class The class to get the config for
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
<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller;

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

    /**
     * @covers \Weasel\JsonMarshaller\JsonMapper
     */
    public function testEncodeWrapperObject()
    {
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';

        $config = new Config\ClassMarshaller();
        $config->serialization = new Config\Serialization\ClassSerialization();
        $config->serialization->typeInfo = new Config\Serialization\TypeInfo();
        $config->serialization->typeInfo->typeInfoAs = Config\Serialization\TypeInfo::TI_AS_WRAPPER_OBJECT;
        $config->serialization->typeInfo->typeInfo = Config\Serialization\TypeInfo::TI_USE_CLASS;
        $config->serialization->typeInfo->subTypes[$mtc] = $mtc;
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $object = new MockTestClass();
        $object->blah = new MockTestClassB();

        $expected = array(
            $mtc => array()
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
        $configProvider = new MockedConfigProvider();
        $mtc = 'Weasel\JsonMarshaller\MockTestClass';
        $mtcb = 'Weasel\JsonMarshaller\MockTestClassB';

        $config = new Config\ClassMarshaller();
        $config->serialization = new Config\Serialization\ClassSerialization();
        $config->serialization->typeInfo = new Config\Serialization\TypeInfo();
        $config->serialization->typeInfo->typeInfoAs = Config\Serialization\TypeInfo::TI_AS_WRAPPER_ARRAY;
        $config->serialization->typeInfo->typeInfo = Config\Serialization\TypeInfo::TI_USE_CLASS;
        $config->serialization->typeInfo->subTypes[$mtcb] = $mtcb;
        $configProvider->fakeConfig[$mtcb] = $config;

        $config = new Config\ClassMarshaller();
        $config->serialization = new Config\Serialization\ClassSerialization();
        $prop = new Config\Serialization\DirectSerialization();
        $prop->type = $mtcb;
        $prop->property = 'blah';
        $config->serialization->properties['blah'] = $prop;
        $configProvider->fakeConfig[$mtc] = $config;

        $mapper = new JsonMapper($configProvider);

        $object = new MockTestClass();
        $object->blah = new MockTestClassB();

        $expected = array(
            "blah" => array(
                $mtcb,
                array()
            )
        );
        $result = $mapper->writeArray($object);
        $this->assertInternalType("array", $result);
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

class MockedConfigProvider implements \Weasel\JsonMarshaller\Config\JsonConfigProvider
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
<?php
namespace Weasel\JsonMarshaller\Config;

use Weasel\Annotation\AnnotationConfigurator;
use Mockery as m;
use Weasel\Annotation\AnnotationReaderFactory;
use Weasel\JsonMarshaller\Config\Annotations\JsonProperty;
use Weasel\JsonMarshaller\Config\Serialization\ClassSerialization;
use Weasel\JsonMarshaller\Config\Serialization\GetterSerialization;

class ClassAnnotationDriverTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\JsonMarshaller\Config\ClassAnnotationDriver::_configureGetter
     */
    public function testGetterNameGuess()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\ClassAnnotationDriverTestClassA');

        $mockReader = m::mock('\Weasel\Common\Annotation\IAnnotationReader');
        $mockReader->shouldReceive('getSingleClassAnnotation')->withAnyArgs()->andReturnNull();
        $mockReader->shouldReceive('getSingleMethodAnnotation')->with('getStuff',
            '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty')->andReturn(
                new JsonProperty(null, "string", null)
            );
        $mockReader->shouldReceive('getSingleMethodAnnotation')->with('isGood',
            '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty')->andReturn(
                new JsonProperty(null, "bool", null)
            );
        $mockReader->shouldReceive('getSingleMethodAnnotation')->withAnyArgs()->andReturnNull();

        $mockReaderFactory = m::mock('\Weasel\Annotation\AnnotationReaderFactory');
        $mockReaderFactory->shouldReceive('getReaderForClass')->with($rClass)->andReturn($mockReader);

        /**
         * @var \Weasel\Annotation\AnnotationReaderFactory $mockReaderFactory
         */
        $driver = new ClassAnnotationDriver($rClass, $mockReaderFactory);

        $config = $driver->getConfig();

        $eProperties = array(
            "stuff" => new GetterSerialization("getStuff", "string", 1),
            "good" => new GetterSerialization("isGood", "bool", 1),
        );

        $this->assertEquals($eProperties, $config->serialization->properties);
    }

    /**
     * @covers \Weasel\JsonMarshaller\Config\ClassAnnotationDriver::_configureGetter
     */
    public function testGetterNameIsNoBool()
    {

        $rClass = new \ReflectionClass('\Weasel\JsonMarshaller\Config\ClassAnnotationDriverTestClassA');

        $mockReader = m::mock('\Weasel\Common\Annotation\IAnnotationReader');
        $mockReader->shouldReceive('getSingleClassAnnotation')->withAnyArgs()->andReturnNull();
        $mockReader->shouldReceive('getSingleMethodAnnotation')->with('getStuff',
            '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty')->andReturn(
                new JsonProperty(null, "string", null)
            );
        $mockReader->shouldReceive('getSingleMethodAnnotation')->with('isGood',
            '\Weasel\JsonMarshaller\Config\Annotations\JsonProperty')->andReturn(
                new JsonProperty(null, "string", null)
            );
        $mockReader->shouldReceive('getSingleMethodAnnotation')->withAnyArgs()->andReturnNull();

        $mockReaderFactory = m::mock('\Weasel\Annotation\AnnotationReaderFactory');
        $mockReaderFactory->shouldReceive('getReaderForClass')->with($rClass)->andReturn($mockReader);

        /**
         * @var \Weasel\Annotation\AnnotationReaderFactory $mockReaderFactory
         */
        $driver = new ClassAnnotationDriver($rClass, $mockReaderFactory);

        $config = $driver->getConfig();

        $eProperties = array(
            "stuff" => new GetterSerialization("getStuff", "string", 1),
            "isGood" => new GetterSerialization("isGood", "string", 1),
        );

        $this->assertEquals($eProperties, $config->serialization->properties);

    }

}

class ClassAnnotationDriverTestClassA
{

    public function getStuff()
    {

    }

    public function isGood()
    {

    }

}

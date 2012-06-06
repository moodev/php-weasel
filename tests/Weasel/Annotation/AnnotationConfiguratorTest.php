<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;
require_once(__DIR__ . '/../../../lib/WeaselAutoloader.php');

class AnnotationConfiguratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testBuiltIns()
    {

        $mock = $this->getMock('\Weasel\Annotation\AnnotationReaderFactory', array(), array(), '', false);
        $mock->expects($this->never())->method('getReaderForClass');
        $instance = new AnnotationConfigurator(null, $mock);

        $annotation = $instance->get('\Weasel\Annotation\Config\Annotations\Annotation');

        $builtins = \Weasel\Annotation\Config\BuiltInsProvider::getConfig();
        $this->assertSame($builtins->getAnnotation('\Weasel\Annotation\Config\Annotations\Annotation'), $annotation);

    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testBasicAnnotation()
    {

        $annotation = new Config\Annotations\Annotation(array('class'), 6);
        $classAnnotations = array('\Weasel\Annotation\Config\Annotations\Annotation' => array($annotation));

        $mock =
            $this->getMock('\Weasel\Annotation\AnnotationReader',
                           array('getClassAnnotations',
                                 'getMethodAnnotations',
                                 'getPropertyAnnotations'
                           ), array(), '', false
            );
        $mock->expects($this->at(0))->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->at(1))->method('getMethodAnnotations')->with($this->equalTo("__construct"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(2))->method('getMethodAnnotations')->with($this->equalTo("setA"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(3))->method('getMethodAnnotations')->with($this->equalTo("getA"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(4))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(5))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);

        $this->assertEquals($expected, $result);

    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testCreator()
    {

        $annotation = new Config\Annotations\Annotation(array('class'), 6);
        $classAnnotations = array('\Weasel\Annotation\Config\Annotations\Annotation' => array($annotation));

        $constructorParams = array();
        $constructorParams[] = new Config\Annotations\Parameter("foo", "string", true);
        $constructorParams[] = new Config\Annotations\Parameter("bar", "integer", true);
        $constructorParams[] = new Config\Annotations\Parameter("baz", "boolean", false);

        $constructorAnnotation = new Config\Annotations\AnnotationCreator($constructorParams);

        $constructorAnnotations =
            array('\Weasel\Annotation\Config\Annotations\AnnotationCreator' => array($constructorAnnotation));


        $mock =
            $this->getMock('\Weasel\Annotation\AnnotationReader',
                           array('getClassAnnotations',
                                 'getMethodAnnotations',
                                 'getPropertyAnnotations'
                           ), array(), '', false
            );
        $mock->expects($this->at(0))->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->at(1))->method('getMethodAnnotations')->with($this->equalTo("__construct"))
            ->will($this->returnValue($constructorAnnotations));
        $mock->expects($this->at(2))->method('getMethodAnnotations')->with($this->equalTo("setA"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(3))->method('getMethodAnnotations')->with($this->equalTo("getA"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(4))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(5))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->setCreatorMethod('__construct');
        $expected->addCreatorParam(new Config\Param("foo", "string", true));
        $expected->addCreatorParam(new Config\Param("bar", "integer", true));
        $expected->addCreatorParam(new Config\Param("baz", "boolean", false));

        $this->assertEquals($expected, $result);

    }

    public function testStaticCreator()
    {

        $annotation = new Config\Annotations\Annotation(array('class'), 6);
        $classAnnotations = array('\Weasel\Annotation\Config\Annotations\Annotation' => array($annotation));

        $constructorParams = array();
        $constructorParams[] = new Config\Annotations\Parameter("foo", "string", true);

        $constructorAnnotation = new Config\Annotations\AnnotationCreator($constructorParams);

        $constructorAnnotations =
            array('\Weasel\Annotation\Config\Annotations\AnnotationCreator' => array($constructorAnnotation));


        $mock =
            $this->getMock('\Weasel\Annotation\AnnotationReader',
                           array('getClassAnnotations',
                                 'getMethodAnnotations',
                                 'getPropertyAnnotations'
                           ), array(), '', false
            );
        $mock->expects($this->at(0))->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->at(1))->method('getMethodAnnotations')->with($this->equalTo("__construct"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(2))->method('getMethodAnnotations')->with($this->equalTo("setA"))
            ->will($this->returnValue($constructorAnnotations));
        $mock->expects($this->at(3))->method('getMethodAnnotations')->with($this->equalTo("getA"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(4))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $mock->expects($this->at(5))->method('getPropertyAnnotations')->with($this->equalTo("a"))
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->setCreatorMethod('setA');
        $expected->addCreatorParam(new Config\Param("foo", "string", true));

        $this->assertEquals($expected, $result);
    }

    public function testNonStaticCreatorFail()
    {

    }
}

class MockAnnotationReaderFactory extends AnnotationReaderFactory
{

    public $mock;

    public function __construct($mock = null)
    {
        $this->mock = $mock;
    }

    public function getReaderForClass(\ReflectionClass $class, AnnotationConfigProvider $configProvider)
    {
        return $this->mock;
    }
}

class BoringAnnotation
{
    public $a;

    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a =
            array($a,
                  $b,
                  $c
            );
    }

    public function setA($a)
    {
        $this->a = $a;
        return $this;
    }

    public function getA()
    {
        return $this->a;
    }

}

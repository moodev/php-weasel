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
        $instance = new AnnotationConfigurator(null, null, $mock);

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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array(
                                                  "__construct",
                                                  array()
                                              ),
                                              array(
                                                  "setA",
                                                  array()
                                              ),
                                              array(
                                                  "getA",
                                                  array()
                                              ),
                                              array(
                                                  "creator",
                                                  array()
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));

        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

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
        $constructorParams[] = new Config\Annotations\Parameter(null, "boolean", false);

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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    $constructorAnnotations
                                              ),
                                              array("setA",
                                                    array()
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    array()
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->setCreatorMethod('__construct');
        $expected->addCreatorParam(new Config\Param("foo", "string", true));
        $expected->addCreatorParam(new Config\Param("bar", "integer", true));
        $expected->addCreatorParam(new Config\Param("c", "boolean", false));

        $this->assertEquals($expected, $result, "Got " . print_r($result, true));

    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    array()
                                              ),
                                              array("setA",
                                                    array()
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    $constructorAnnotations
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->setCreatorMethod('creator');
        $expected->addCreatorParam(new Config\Param("foo", "string", true));

        $this->assertEquals($expected, $result);
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testProperties()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValueMap(array(
                                              array("a",
                                                    array('\Weasel\Annotation\Config\Annotations\Property' => array(new Config\Annotations\Property("string"))),
                                              ),
                                              array("b",
                                                    array('\Weasel\Annotation\Config\Annotations\Property' => array(new Config\Annotations\Property("float"))),
                                              ),
                                         )
                   )
        );
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->addProperty(new Config\Property("a", "string"));
        $expected->addProperty(new Config\Property("b", "float"));

        $this->assertEquals($expected, $result, "Got " . print_r($result, true));
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testEnum()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValueMap(array(
                                              array("a",
                                                    array()
                                              ),
                                              array("b",
                                                    array()
                                              ),
                                              array("enumTest",
                                                    array('\Weasel\Annotation\Config\Annotations\Enum' => array(
                                                        new Config\Annotations\Enum("toast")
                                                    )
                                                    )
                                              )
                                         )
                   )
        );
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->addEnum(new Config\Enum("toast", array("FOO" => 1,
                                                          "BAR" => 2
                                                    ))
        );

        $this->assertEquals($expected, $result, "Got " . print_r($result, true));
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testEnumDefaultName()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValueMap(array(
                                              array("a",
                                                    array()
                                              ),
                                              array("b",
                                                    array()
                                              ),
                                              array("enumTest",
                                                    array('\Weasel\Annotation\Config\Annotations\Enum' => array(
                                                        new Config\Annotations\Enum(null)
                                                    )
                                                    )
                                              )
                                         )
                   )
        );
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $result = $instance->get('\Weasel\Annotation\BoringAnnotation');

        $expected = new Config\Annotation('\Weasel\Annotation\BoringAnnotation', array('class'), 6);
        $expected->addEnum(new Config\Enum("enumTest", array("FOO" => 1,
                                                             "BAR" => 2
                                                       ))
        );

        $this->assertEquals($expected, $result, "Got " . print_r($result, true));
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Enums must be static properties
     */
    public function testNonStaticEnum()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValueMap(array(
                                              array("a",
                                                    array('\Weasel\Annotation\Config\Annotations\Enum' => array(
                                                        new Config\Annotations\Enum(null)
                                                    )
                                                    )
                                              ),
                                         )
                   )
        );
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Enum must be an array
     */
    public function testNonArrayEnum()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValueMap(array(
                                              array("b",
                                                    array('\Weasel\Annotation\Config\Annotations\Enum' => array(
                                                        new Config\Annotations\Enum(null)
                                                    )
                                                    )
                                              ),
                                         )
                   )
        );
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Did not find an @Annotation annotation on
     */
    public function testThatsNoAnnotation()
    {

        $mock =
            $this->getMock('\Weasel\Annotation\AnnotationReader',
                           array('getClassAnnotations',
                                 'getMethodAnnotations',
                                 'getPropertyAnnotations'
                           ), array(), '', false
            );
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue(array()));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    array()
                                              ),
                                              array("setA",
                                                    array()
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    array()
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');

    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Non-static methods cannot be configured as creators
     */
    public function testNonStaticCreatorFail()
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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    array()
                                              ),
                                              array("setA",
                                                    $constructorAnnotations
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    array()
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');

    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Creator args don't match with method args
     */
    public function testTooManyCreatorArgs()
    {

        $annotation = new Config\Annotations\Annotation(array('class'), 6);
        $classAnnotations = array('\Weasel\Annotation\Config\Annotations\Annotation' => array($annotation));

        $constructorParams = array();
        $constructorParams[] = new Config\Annotations\Parameter("foo", "string", true);
        $constructorParams[] = new Config\Annotations\Parameter("bar", "string", true);

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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    array()
                                              ),
                                              array("setA",
                                                    array()
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    $constructorAnnotations
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');
    }

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Creator args don't match with method args
     */
    public function testTooFewCreatorArgs()
    {

        $annotation = new Config\Annotations\Annotation(array('class'), 6);
        $classAnnotations = array('\Weasel\Annotation\Config\Annotations\Annotation' => array($annotation));

        $constructorParams = array();
        $constructorParams[] = new Config\Annotations\Parameter("foo", "string", true);
        $constructorParams[] = new Config\Annotations\Parameter("bar", "string", true);

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
        $mock->expects($this->any())->method('getClassAnnotations')->will($this->returnValue($classAnnotations));
        $mock->expects($this->any())->method('getMethodAnnotations')
            ->will($this->returnValueMap(array(
                                              array("__construct",
                                                    $constructorAnnotations
                                              ),
                                              array("setA",
                                                    array()
                                              ),
                                              array("getA",
                                                    array()
                                              ),
                                              array("creator",
                                                    array()
                                              ),
                                         )
                   )
        );
        $mock->expects($this->any())->method('getPropertyAnnotations')
            ->will($this->returnValue(array()));
        $instance = new AnnotationConfigurator(null, null, new MockAnnotationReaderFactory($mock));

        $instance->get('\Weasel\Annotation\BoringAnnotation');
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

    public static $b = 666;

    public static $enumTest = array(
        "FOO" => 1,
        "BAR" => 2,
    );

    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a =
            array($a,
                  $b,
                  $c
            );
    }

    public static function creator($a = null)
    {
        return new BoringAnnotation($a);
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

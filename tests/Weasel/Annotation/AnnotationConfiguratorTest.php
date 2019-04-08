<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation;

use PHPUnit\Framework\TestCase;
use Weasel\Annotation\AnnotationReaderFactory;
use Weasel\Annotation\Config\Annotations\Annotation;

class AnnotationConfiguratorTest extends TestCase
{

    /**
     * @covers \Weasel\Annotation\AnnotationConfigurator
     */
    public function testBuiltIns()
    {
        $mock = $this->createMock(AnnotationReaderFactory::class);
        $mock->expects($this->never())->method('getReaderForClass');

        $instance = new AnnotationConfigurator(null, null, $mock);

        $annotation = $instance->get(Annotation::class);

        $builtins = \Weasel\Annotation\Config\BuiltInsProvider::getConfig();
        $this->assertSame($builtins->getAnnotation(Annotation::class), $annotation);

    }

}

class MockAnnotationReaderFactory extends AnnotationReaderFactory
{

    public $mock;

    public function __construct($mock = null)
    {
        $this->mock = $mock;
    }

    public function getReaderForClass(\ReflectionClass $class)
    {
        return $this->mock;
    }
}

class BoringAnnotation
{
    public $a;

    public static $b = 666;

    public static $enumTest = [
        "FOO" => 1,
        "BAR" => 2,
    ];

    public function __construct($a = null, $b = null, $c = null)
    {
        $this->a = [$a, $b, $c];
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

<?php
namespace Weasel\Annotation\Tests;

require_once(__DIR__ . '/../../../WeaselAutoloader.php');

use Weasel\Annotation\DocblockParser;
use Weasel\Annotation\AnnotationConfigurator;

class DocblockParserTest extends \PHPUnit_Framework_TestCase
{

    public function provideSimpleClassAnnotation() {
        return array(
            array('flibble fish', 'string'),
            array('test "some" quotes', 'string'),
            array(12356, 'integer'),
            array(5.23, 'float'),
            array(true, 'boolean'),
            array(false, 'boolean'),
        );
    }

    protected function _phpTypeToAnnotationType($type, $value) {
        if ($type === 'string') {
            return '"'.str_replace('"', '""', $value).'"';
        }
        if ($type === 'boolean') {
            return $value ? "true" : "false";
        }
        return $value;
    }


    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testSimpleClassAnnotation($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', $type));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo='.$valueQuoted.')
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = $value;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArrayClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', 'string[][]'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo={{"ab", "cd"}, {"ef"}, {}})
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array(array("ab", "cd"), array("ef"), array());

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArraySingleElementClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', 'string[]'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo="bar")
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array("bar");

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testNestedClassAnnotation($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', '\Weasel\Annotation\Tests\Glarp'));
        $mockConfigurator->addAnnotation($annotation);
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Glarp', array('\Weasel\Annotation\Tests\Gloop'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('bar', $type));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=@Glarp(bar='.$valueQuoted.'))
              */',
            "class",
            array(
                'Gloop' => 'Weasel\Annotation\Tests\Gloop',
                'Glarp' => 'Weasel\Annotation\Tests\Glarp'
            )

        );

        $gloop = new Gloop();
        $gloop->foo = new Glarp();
        $gloop->foo->bar = $value;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testSimpleClassAnnotationCreator($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(
            new \Weasel\Annotation\Config\Param('foo', $type, false)
        );
        $annotation->addCreatorParam(
            new \Weasel\Annotation\Config\Param('baz', $type, false)
        );
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo='.$valueQuoted.')
              * @Gloop('.$valueQuoted.')
              * @Gloop('.$valueQuoted.', '.$valueQuoted.')
              * @Gloop(baz='.$valueQuoted.')
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $expected = array();

        $gloop = new Gloop();
        $gloop->fromca = $value;
        $expected[] = $gloop;

        $gloop = new Gloop();
        $gloop->fromca = $value;
        $expected[] = $gloop;

        $gloop = new Gloop();
        $gloop->fromca = $gloop->fromcb = $value;
        $expected[] = $gloop;

        $gloop = new Gloop();
        $gloop->fromcb = $value;
        $expected[] = $gloop;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => $expected), $parsed);

    }

    /**
     * @return void
     */
    public function testUnknownAnnotations() {

        $mockConfigurator = new MockConfigurator();

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @author Jonathan Oddy <jonathan@moo.com>
              * @param string $value Wheeeeee
              * @param string[] $type Wobble Kerping Splat
              * @returns your mum
              */',
            "method",
            array()
        );

        $this->assertEquals(array(), $parsed);

    }

    public function testEnumAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', 'integer'));
        $annotation->addEnum(new \Weasel\Annotation\Config\Enum('Snorks', array("FOO" => 1, "BAR" => 2, "BAZ" => 3)));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=Gloop.Snorks.BAR)
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = 2;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testEnumAnnotationNoName() {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(new \Weasel\Annotation\Config\Param('foo', 'integer', false));
        $annotation->addCreatorParam(new \Weasel\Annotation\Config\Param('baz', 'integer', false));
        $annotation->addEnum(new \Weasel\Annotation\Config\Enum('Snorks', array("FOO" => 1, "BAR" => 2, "BAZ" => 3)));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(Gloop.Snorks.BAR)
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->fromca = 2;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }
}

class Gloop {

    public $foo;

    public $fromca;
    public $fromcb;

    public function __construct($foo = null, $baz = null) {
        $this->fromca = $foo;
        $this->fromcb = $baz;
    }
}

class Glarp {

    public $bar;

    public $fromca;
    public $fromcb;

    public function __construct($bar = null, $baz = null) {
        $this->fromca = $bar;
        $this->fromcb = $baz;
    }

}

class MockConfigurator extends AnnotationConfigurator{

    protected $config;

    public function addAnnotation($annotation) {
        $this->config->addAnnotation($annotation);
    }

    public function __construct() {
        $this->config = new \Weasel\Annotation\Config\AnnotationConfig();
    }

    public function get($type) {
        return $this->config->getAnnotation($type);
    }

    public function getLogger() {
        $logger = new \Weasel\Logger\FileLogger();
        $logger->setLogLevel(\Weasel\Logger\Logger::LOG_LEVEL_DEBUG);
    }

}


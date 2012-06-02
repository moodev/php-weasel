<?php
namespace PhpAnnotation\Tests;

require_once(__DIR__ . '/../../WeaselAutoloader.php');

use PhpAnnotation\DocblockParser;
use PhpAnnotation\AnnotationConfigurator;

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
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => $type
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo='.$valueQuoted.')
              */',
            "class",
            array('Gloop' => 'Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = $value;

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArrayClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => 'string[][]'
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo={{"ab", "cd"}, {"ef"}, {}})
              */',
            "class",
            array('Gloop' => 'Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array(array("ab", "cd"), array("ef"), array());

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArraySingleElementClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => 'string[]'
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo="bar")
              */',
            "class",
            array('Gloop' => 'Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array("bar");

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testNestedClassAnnotation($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => '\Annotation\Tests\Glarp'
                )
        );
        $mockConfigurator->types['\Annotation\Tests\Glarp'] =
            array(
                'class' => '\Annotation\Tests\Glarp',
                'on' => array('\Annotation\Tests\Gloop'),
                'properties' => array(
                    'bar' => $type
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=@Glarp(bar='.$valueQuoted.'))
              */',
            "class",
            array(
                'Gloop' => 'Annotation\Tests\Gloop',
                'Glarp' => 'Annotation\Tests\Glarp'
            )

        );

        $gloop = new Gloop();
        $gloop->foo = new Glarp();
        $gloop->foo->bar = $value;

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testSimpleClassAnnotationCreator($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'creatorMethod' => '__construct',
                'creatorParams' => array(
                    array(
                        'name' => 'foo',
                        'type' => $type
                        ),
                    array(
                        'name' => 'baz',
                        'type' => $type
                    )
                )
            );

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
            array('Gloop' => 'Annotation\Tests\Gloop')
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

        $this->assertEquals(array('\Annotation\Tests\Gloop' => $expected), $parsed);

    }

    /**
     * @param $value
     * @param $type
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
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => 'integer'
                ),
                'enums' => array(
                    'Snorks' => array(
                        "FOO" => 1,
                        "BAR" => 2,
                        "BAZ" => 3,
                    )
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=Gloop.Snorks.BAR)
              */',
            "class",
            array('Gloop' => 'Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = 2;

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testEnumAnnotationNoName() {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\Annotation\Tests\Gloop'] =
            array(
                'class' => '\Annotation\Tests\Gloop',
                'on' => array('class'),
                'creatorMethod' => '__construct',
                'creatorParams' => array(
                    array(
                        'name' => 'foo',
                        'type' => 'integer'
                    ),
                    array(
                        'name' => 'baz',
                        'type' => 'integer'
                    )
                ),
                'enums' => array(
                    'Snorks' => array(
                        "FOO" => 1,
                        "BAR" => 2,
                        "BAZ" => 3,
                    )
                )
            );

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop(Gloop.Snorks.BAR)
              */',
            "class",
            array('Gloop' => 'Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->fromca = 2;

        $this->assertEquals(array('\Annotation\Tests\Gloop' => array($gloop)), $parsed);

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

    public $types = array();

    public function get($type) {
        return $this->types[$type];
    }

    public function getLogger() {
        $logger = new \PhpLogger\FileLogger();
        $logger->setLogLevel(\PhpLogger\Logger::LOG_LEVEL_DEBUG);
    }

}


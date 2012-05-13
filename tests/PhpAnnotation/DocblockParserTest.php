<?php
namespace PhpAnnotation\Tests;

require_once(__DIR__ . '/../../PhpAnnotation/autoloader.php');

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
        $mockConfigurator->types['\PhpAnnotation\Tests\Gloop'] =
            array(
                'class' => '\PhpAnnotation\Tests\Gloop',
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
            array('Gloop' => 'PhpAnnotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = $value;

        $this->assertEquals(array('\PhpAnnotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArrayClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\PhpAnnotation\Tests\Gloop'] =
            array(
                'class' => '\PhpAnnotation\Tests\Gloop',
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
            array('Gloop' => 'PhpAnnotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array(array("ab", "cd"), array("ef"), array());

        $this->assertEquals(array('\PhpAnnotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    public function testArraySingleElementClassAnnotation() {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\PhpAnnotation\Tests\Gloop'] =
            array(
                'class' => '\PhpAnnotation\Tests\Gloop',
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
            array('Gloop' => 'PhpAnnotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = array("bar");

        $this->assertEquals(array('\PhpAnnotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testNestedClassAnnotation($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\PhpAnnotation\Tests\Gloop'] =
            array(
                'class' => '\PhpAnnotation\Tests\Gloop',
                'on' => array('class'),
                'properties' => array(
                    'foo' => '\PhpAnnotation\Tests\Glarp'
                )
        );
        $mockConfigurator->types['\PhpAnnotation\Tests\Glarp'] =
            array(
                'class' => '\PhpAnnotation\Tests\Glarp',
                'on' => array('\PhpAnnotation\Tests\Gloop'),
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
                'Gloop' => 'PhpAnnotation\Tests\Gloop',
                'Glarp' => 'PhpAnnotation\Tests\Glarp'
            )

        );

        $gloop = new Gloop();
        $gloop->foo = new Glarp();
        $gloop->foo->bar = $value;

        $this->assertEquals(array('\PhpAnnotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testSimpleClassAnnotationCreator($value, $type) {

        $mockConfigurator = new MockConfigurator();
        $mockConfigurator->types['\PhpAnnotation\Tests\Gloop'] =
            array(
                'class' => '\PhpAnnotation\Tests\Gloop',
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
            array('Gloop' => 'PhpAnnotation\Tests\Gloop')
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

        $this->assertEquals(array('\PhpAnnotation\Tests\Gloop' => $expected), $parsed);

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

}


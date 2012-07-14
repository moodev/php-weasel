<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Tests;

require_once(__DIR__ . '/../../../lib/WeaselAutoloader.php');

use Weasel\Annotation\DocblockParser;
use Weasel\Annotation\AnnotationConfigurator;

class DocblockParserTest extends \PHPUnit_Framework_TestCase
{

    public function provideSimpleClassAnnotation()
    {
        return array(
            array('flibble fish',
                  'string'
            ),
            array('test "some" quotes',
                  'string'
            ),
            array(12356,
                  'integer'
            ),
            array(5.23,
                  'float'
            ),
            array(true,
                  'boolean'
            ),
            array(false,
                  'boolean'
            ),
        );
    }

    protected function _phpTypeToAnnotationType($type, $value)
    {
        if ($type === 'string') {
            return '"' . str_replace('"', '""', $value) . '"';
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
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testSimpleClassAnnotation($value, $type)
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', $type));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=' . $valueQuoted . ')
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();
        $gloop->foo = $value;

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testNoArgsAnnotation()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testTwoAnnotations()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop
              * @Gloop
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop,
                                                                            $gloop
                             )
                            ), $parsed
        );

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testEmptyArgsAnnotation()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Gloop()
              */',
            "class",
            array('Gloop' => 'Weasel\Annotation\Tests\Gloop')
        );

        $gloop = new Gloop();

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testFullyNamespacedAnnotation()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @\Weasel\Annotation\Tests\Gloop
              */',
            "class",
            array()
        );

        $gloop = new Gloop();

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testMissingWhitespaceNoArgs()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @\Weasel\Annotation\Tests\Gloop@glarp
              */',
            "class",
            array()
        );

        $this->assertEmpty($parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testMissingWhitespaceArgs()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @\Weasel\Annotation\Tests\Gloop()@glarp
              */',
            "class",
            array()
        );

        $this->assertEmpty($parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testArrayClassAnnotation()
    {

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
        $gloop->foo =
            array(array("ab",
                        "cd"
                  ),
                  array("ef"),
                  array()
            );

        $this->assertEquals(array('\Weasel\Annotation\Tests\Gloop' => array($gloop)), $parsed);

    }

    /**
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testArraySingleElementClassAnnotation()
    {

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
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testNestedClassAnnotation($value, $type)
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', '\Weasel\Annotation\Tests\Glarp'));
        $mockConfigurator->addAnnotation($annotation);
        $annotation =
            new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Glarp', array('\Weasel\Annotation\Tests\Gloop'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('bar', $type));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $this->_phpTypeToAnnotationType($type, $value);

        $parsed = $parser->parse(
            '/**
              * @Gloop(foo=@Glarp(bar=' . $valueQuoted . '))
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
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testMultiNestedArgs()
    {
        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Multi', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('a', '\Weasel\Annotation\Tests\Glarp'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('b', '\Weasel\Annotation\Tests\Glarp'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('c', '\Weasel\Annotation\Tests\Gloop'));
        $mockConfigurator->addAnnotation($annotation);
        $annotation =
            new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Glarp', array('\Weasel\Annotation\Tests\Multi'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('bar', 'string'));
        $mockConfigurator->addAnnotation($annotation);
        $annotation =
            new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('\Weasel\Annotation\Tests\Multi'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', 'string'));
        $mockConfigurator->addAnnotation($annotation);

        $parser = new DocblockParser($mockConfigurator);

        $parsed = $parser->parse(
            '/**
              * @Multi(a=@Glarp(bar="foo"), b=@Glarp(bar="baa"), c=@Gloop(foo="fnord"))
              * @Multi(a=@Glarp(bar="foo")   , b=@Glarp(bar="baa")  ,  c=@Gloop(foo="fnord"))
              * @Multi(a=@Glarp(bar="foo"),b=@Glarp(bar="baa"),c=@Gloop(foo="fnord"))
              */',
            "class",
            array(
                 'Gloop' => 'Weasel\Annotation\Tests\Gloop',
                 'Glarp' => 'Weasel\Annotation\Tests\Glarp',
                 'Multi' => 'Weasel\Annotation\Tests\Multi'
            )

        );

        $multi = new Multi();
        $multi->a = new Glarp();
        $multi->a->bar = "foo";
        $multi->b = new Glarp();
        $multi->b->bar = "baa";
        $multi->c = new Gloop();
        $multi->c->foo = "fnord";

        $results = array_fill(0, 3, $multi);

        $this->assertEquals(array('\Weasel\Annotation\Tests\Multi' => $results), $parsed);

    }

    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testSimpleClassAnnotationCreator($value, $type)
    {

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
              * @Gloop(foo=' . $valueQuoted . ')
              * @Gloop(' . $valueQuoted . ')
              * @Gloop(' . $valueQuoted . ', ' . $valueQuoted . ')
              * @Gloop(baz=' . $valueQuoted . ')
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
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testUnknownAnnotations()
    {

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

    /**
     * @return void
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testEnumAnnotation()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->addProperty(new \Weasel\Annotation\Config\Property('foo', 'integer'));
        $annotation->addEnum(new \Weasel\Annotation\Config\Enum('Snorks', array("FOO" => 1,
                                                                                "BAR" => 2,
                                                                                "BAZ" => 3
                                                                          ))
        );
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

    /**
     * @return void
     * @covers \Weasel\Annotation\DocblockParser
     */
    public function testEnumAnnotationNoName()
    {

        $mockConfigurator = new MockConfigurator();
        $annotation = new \Weasel\Annotation\Config\Annotation('\Weasel\Annotation\Tests\Gloop', array('class'));
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(new \Weasel\Annotation\Config\Param('foo', 'integer', false));
        $annotation->addCreatorParam(new \Weasel\Annotation\Config\Param('baz', 'integer', false));
        $annotation->addEnum(new \Weasel\Annotation\Config\Enum('Snorks', array("FOO" => 1,
                                                                                "BAR" => 2,
                                                                                "BAZ" => 3
                                                                          ))
        );
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

class Gloop
{

    public $foo;

    public $fromca;
    public $fromcb;

    public function __construct($foo = null, $baz = null)
    {
        $this->fromca = $foo;
        $this->fromcb = $baz;
    }
}

class Multi
{

    public $a;
    public $b;
    public $c;

}

class Glarp
{

    public $bar;

    public $fromca;
    public $fromcb;

    public function __construct($bar = null, $baz = null)
    {
        $this->fromca = $bar;
        $this->fromcb = $baz;
    }

}

class MockConfigurator extends AnnotationConfigurator
{

    protected $config;

    public function addAnnotation($annotation)
    {
        $this->config->addAnnotation($annotation);
    }

    public function __construct()
    {
        $this->config = new \Weasel\Annotation\Config\AnnotationConfig();
    }

    public function get($type)
    {
        return $this->config->getAnnotation($type);
    }

    public function getLogger()
    {
        $logger = new \Weasel\Common\Logger\FileLogger();
        $logger->setLogLevel(\Weasel\Common\Logger\Logger::LOG_LEVEL_DEBUG);
    }

}


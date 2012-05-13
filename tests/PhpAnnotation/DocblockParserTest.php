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


    /**
     * @param $value
     * @param $type
     * @dataProvider provideSimpleClassAnnotation
     */
    public function testSimpleClassAnnotation($value, $type) {

        $mockConfigurator = $this->getMock('\PhpAnnotation\AnnotationConfigurator');
        $mockConfigurator->expects($this->any())->method('get')->with('\PhpAnnotation\Tests\Gloop')->will(
            $this->returnValue(
                array(
                    'class' => '\PhpAnnotation\Tests\Gloop',
                    'on' => array('class'),
                    'properties' => array(
                        'foo' => $type
                    )
                )
            )
        );

        $parser = new DocblockParser($mockConfigurator);

        $valueQuoted = $value;
        if ($type === 'string') {
            $valueQuoted = '"'.str_replace('"', '""', $value).'"';
        }
        if ($type === 'boolean') {
            $valueQuoted = $value ? "true" : "false";
        }

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

}

class Gloop {

    public $foo;

}


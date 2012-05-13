<?php
namespace PhpAnnotation\Tests;

require_once(__DIR__ . '/../../PhpAnnotation/autoloader.php');

use PhpAnnotation\AnnotationLexer;

class AnnotationLexerTest extends \PHPUnit_Framework_TestCase
{

    public function provideSimpleType() {
        return array(
            array('    123  +942 -954 1 0 +0 -0  +5-1 ', array_fill(0, 9, AnnotationLexer::T_INTEGER)),
            array('    12.3+9.42 -95.4 1.0 0.0 +0.0-0.0 5e5 12E75 -23e93 94e-12', array_fill(0, 11, AnnotationLexer::T_FLOAT)),
            array('    true false
            true', array_fill(0, 3, AnnotationLexer::T_BOOLEAN)),
            array('   @ @   @@', array_fill(0, 4, AnnotationLexer::T_AT)),
            array('    \\\\ \\\\\\', array_fill(0, 5, AnnotationLexer::T_BACKSLASH)),
            array(' "foo bar" """w  he e" "w  @oo""ar gh""me h 123" "hello
            world"', array_fill(0, 4, AnnotationLexer::T_QUOTED_STRING)),
        );

    }

    /**
     * @param $in
     * @param $expectedTypes
     * @dataProvider provideSimpleType
     */
    public function testSimpleType($in, $expectedTypes) {
        $lexer = new AnnotationLexer($in);
        $got = array();
        $toks = array();
        while ($cur = $lexer->read()) {
            $got[] = $cur['type'];
            $toks[] = $cur['token'];
        }
        $this->assertEquals($expectedTypes, $got, 'Type failure on array ' . print_r($toks, true));
    }

}

<?php
/**
 * @author Jonathan Oddy <jonathan@woaf.net>
 * @copyright 2012 Jonathan Oddy
 * @license ISC
 */
namespace Weasel\Annotation\Tests;

require_once(__DIR__ . '/../../../WeaselAutoloader.php');

use Weasel\Annotation\DocblockLexer;

class DocblockLexerTest extends \PHPUnit_Framework_TestCase
{

    public function provideSimpleType()
    {
        return array(
            array('    123  +942 -954 1 0 +0 -0  +5-1 ', array_fill(0, 9, DocblockLexer::T_INTEGER)),
            array('123  +942 -954 1 0 +0 -0  +5-1 ', array_fill(0, 9, DocblockLexer::T_INTEGER)),
            array('    12.3+9.42 -95.4 1.0 0.0 +0.0-0.0 5e5 12E75 -23e93 94e-12', array_fill(0, 11, DocblockLexer::T_FLOAT)),
            array('    true false
            true', array_fill(0, 3, DocblockLexer::T_BOOLEAN)),
            array('   @ @   @@', array_fill(0, 4, DocblockLexer::T_AT)),
            array('    \\\\ \\\\\\', array_fill(0, 5, DocblockLexer::T_BACKSLASH)),
            array(' "foo bar" """w  he e" "w  @oo""ar gh""me h 123" "hello
            world"', array_fill(0, 4, DocblockLexer::T_QUOTED_STRING)),
        );

    }

    /**
     * @param $in
     * @param $expectedTypes
     * @dataProvider provideSimpleType
     */
    public function testSimpleType($in, $expectedTypes)
    {
        $lexer = new DocblockLexer($in);
        $got = array();
        $toks = array();
        $cur = $lexer->skip();
        do {
            $got[] = $cur['type'];
            $toks[] = $cur['token'];
        } while ($cur = $lexer->next(true));
        $this->assertEquals($expectedTypes, $got, 'Type failure on array ' . print_r($toks, true));
    }

}

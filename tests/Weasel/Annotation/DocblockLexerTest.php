<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Tests;

use PHPUnit\Framework\TestCase;
use Weasel\Annotation\DocblockLexer;

class DocblockLexerTest extends TestCase
{

    public function provideSimpleType()
    {
        return array(
            array('    123  +942 -954 1 0 +0 -0  +5-1 ',
                array_fill(0, 9, DocblockLexer::T_INTEGER)
            ),
            array('123  +942 -954 1 0 +0 -0  +5-1 ',
                array_fill(0, 9, DocblockLexer::T_INTEGER)
            ),
            array('    12.3+9.42 -95.4 1.0 0.0 +0.0-0.0 5e5 12E75 -23e93 94e-12',
                array_fill(0, 11, DocblockLexer::T_FLOAT)
            ),
            array('    true false
            true',
                array_fill(0, 3, DocblockLexer::T_BOOLEAN)
            ),
            array('   @ @   @@',
                array_fill(0, 4, DocblockLexer::T_AT)
            ),
            array('    \\\\ \\\\\\',
                array_fill(0, 5, DocblockLexer::T_BACKSLASH)
            ),
            array(' "foo bar" """w  he e" "w  @oo""ar gh""me h 123" "hello
            world"',
                array_fill(0, 4, DocblockLexer::T_QUOTED_STRING)
            ),
            array(':: :',
                array_fill(0, 3, DocblockLexer::T_COLON)
            ),
            array('} }}',
                array_fill(0, 3, DocblockLexer::T_CLOSE_BRACE)
            ),
            array('{ { {',
                array_fill(0, 3, DocblockLexer::T_OPEN_BRACE)
            ),
            array(') ))',
                array_fill(0, 3, DocblockLexer::T_CLOSE_PAREN)
            ),
            array('(( (',
                array_fill(0, 3, DocblockLexer::T_OPEN_PAREN)
            ),
            array(', ,,',
                array_fill(0, 3, DocblockLexer::T_COMMA)
            ),
            array('..    .',
                array_fill(0, 3, DocblockLexer::T_DOT)
            ),
            array('   = = =',
                array_fill(0, 3, DocblockLexer::T_EQUAL)
            ),
            array(' foo bar baz',
                array_fill(0, 3, DocblockLexer::T_IDENTIFIER)
            ),
            array(' null null  null',
                array_fill(0, 3, DocblockLexer::T_NULL)
            ),
            array(' + # ~',
                array_fill(0, 3, DocblockLexer::T_MEH)
            ),
        );

    }

    /**
     * @param $in
     * @param $expectedTypes
     * @dataProvider provideSimpleType
     * @covers \Weasel\Annotation\DocblockLexer
     */
    public function testSimpleType($in, $expectedTypes)
    {
        $lexer = new DocblockLexer($in);
        $got = array();
        $toks = array();
        $peeked[] = $lexer->peek(1, true);
        $cur = $lexer->skip();
        do {
            $this->assertEquals($cur, $lexer->get());
            if ($peek = $lexer->peek(1, true)) {
                $peeked[] = $peek;
            }
            $got[] = $cur['type'];
            $toks[] = $cur['token'];
        } while ($cur = $lexer->next(true));
        $this->assertEquals($expectedTypes, $got, 'Type failure on array ' . print_r($toks, true));
        $this->assertEquals($expectedTypes, $peeked, 'Type failure on array ' . print_r($toks, true));
    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer
     */
    public function testWhiteSpace()
    {
        $lexer = new DocblockLexer("    ");

        $cur = $lexer->get();
        $peek = $lexer->peek();
        $next = $lexer->next();

        $this->assertEquals(DocblockLexer::T_WHITESPACE, $cur["type"]);
        $this->assertNull($peek);
        $this->assertNull($next);

    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer
     */
    public function testEOL()
    {
        $lexer = new DocblockLexer("\n\n\r\n\n");

        $cur = $lexer->get();
        $peek = $lexer->peek();
        $next = $lexer->next();

        $this->assertEquals(DocblockLexer::T_EOL, $cur["type"]);
        $this->assertNull($peek);
        $this->assertNull($next);

    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer
     */
    public function testPreamble()
    {
        $lexer = new DocblockLexer("\n   * \r\n *\t");

        $cur = $lexer->get();
        $peek = $lexer->peek();
        $next = $lexer->next();

        $this->assertEquals(DocblockLexer::T_PREAMBLE, $cur["type"]);
        $this->assertEquals(DocblockLexer::T_PREAMBLE, $peek);
        $this->assertEquals(DocblockLexer::T_PREAMBLE, $next["type"]);

    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer::peek
     * @covers \Weasel\Annotation\DocblockLexer::cur
     * @covers \Weasel\Annotation\DocblockLexer::_wsSkippingPeek
     */
    public function testPeek()
    {

        $testIn = '@ 1   \     true   "foo"  bar';
        $lexer = new DocblockLexer($testIn);

        $cur = $lexer->cur();
        $this->assertEquals(DocblockLexer::T_AT, $lexer->peek(0));
        $this->assertEquals(DocblockLexer::T_WHITESPACE, $lexer->peek(1));
        $this->assertEquals(DocblockLexer::T_INTEGER, $lexer->peek(2));
        $this->assertEquals(DocblockLexer::T_IDENTIFIER, $lexer->peek(10));
        $this->assertNull($lexer->peek(11));

        $this->assertEquals(DocblockLexer::T_AT, $lexer->peek(0, true));
        $this->assertEquals(DocblockLexer::T_INTEGER, $lexer->peek(1, true));
        $this->assertEquals(DocblockLexer::T_BACKSLASH, $lexer->peek(2, true));
        $this->assertEquals(DocblockLexer::T_BOOLEAN, $lexer->peek(3, true));

        $this->assertEquals(DocblockLexer::T_IDENTIFIER, $lexer->peek(5, true));
        $this->assertNull($lexer->peek(6, true));
        $this->assertEquals(DocblockLexer::T_INTEGER, $lexer->peek(1, true));
        $this->assertEquals($cur, $lexer->cur());

        $lexer->next();
        $this->assertEquals(DocblockLexer::T_INTEGER, $lexer->peek(0, true));

    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer::peek
     * @expectedException \InvalidArgumentException
     */
    public function testBadPeek()
    {
        $testIn = '@ 1   \     true   "foo"  bar';
        $lexer = new DocblockLexer($testIn);
        $lexer->peek(-1);
    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer::seek
     * @covers \Weasel\Annotation\DocblockLexer::cur
     */
    public function testSeek()
    {
        $testIn = '@ 1   \     true   "foo"  bar';
        $lexer = new DocblockLexer($testIn);

        $this->assertEquals(0, $lexer->cur());
        $cur = $lexer->seek(4);
        $this->assertEquals(4, $lexer->cur());
        $this->assertEquals(DocblockLexer::T_BACKSLASH, $cur["type"]);
        $this->assertEquals($lexer->get(), $cur);
        $cur = $lexer->seek(-3);
        $this->assertEquals(8, $lexer->cur());
        $this->assertEquals(DocblockLexer::T_QUOTED_STRING, $cur["type"]);
        $this->assertEquals($lexer->get(), $cur);
        $this->assertNull($lexer->seek(12));
        $this->assertEquals(8, $lexer->cur());
        $cur = $lexer->seek(0);
        $this->assertEquals(0, $lexer->cur());
        $this->assertEquals(DocblockLexer::T_AT, $cur["type"]);
        $this->assertEquals($lexer->get(), $cur);
    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer::skipToType
     */
    public function testSkipToType()
    {
        $testIn = '@ 1   \     true   "foo"  bar';
        $lexer = new DocblockLexer($testIn);

        $cur = $lexer->skipToType(DocblockLexer::T_AT);
        $this->assertEquals(DocblockLexer::T_AT, $cur["type"]);
        $this->assertEquals($lexer->get(), $cur);

        $cur = $lexer->skipToType(DocblockLexer::T_QUOTED_STRING);
        $this->assertEquals(DocblockLexer::T_QUOTED_STRING, $cur["type"]);
        $this->assertEquals($lexer->get(), $cur);

        $this->assertNull($lexer->skipToType(DocblockLexer::T_COLON));

    }

    /**
     * @covers \Weasel\Annotation\DocblockLexer::skipToType
     */
    public function testSkipToTypeEmpty()
    {
        $lexer = new DocblockLexer("");

        $this->assertNull($lexer->skipToType(DocblockLexer::T_AT));

    }


}

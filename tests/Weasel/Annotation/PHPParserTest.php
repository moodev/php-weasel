<?php
namespace Weasel\Annotation;
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */

use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PHPParserTest extends TestCase
{


    public function provideClasses()
    {
        return array(
            array('\Weasel\Annotation\TestResources\SimpleNamespaced', 'SimpleNamespaced', array(
                '' => 'Weasel\Annotation\TestResources',
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                'TestB' => 'Weasel\Annotation\TestResources\TestB',
                'Test' => 'Weasel\Annotation\TestResources\BlahFoo',
            )),
            array('\Weasel\Annotation\TestResources\SimpleNamespacedSibling', 'SimpleNamespaced', array(
                '' => 'Weasel\Annotation\TestResources',
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                'TestB' => 'Weasel\Annotation\TestResources\TestB',
                'Test' => 'Weasel\Annotation\TestResources\BlahFoo',
            )),
            array('\Weasel\Annotation\TestResources\SimpleNamespacedExtender', 'SimpleNamespacedExtends', array(
                '' => 'Weasel\Annotation\TestResources',
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                'TestB' => 'Weasel\Annotation\TestResources\TestB',
                'Test' => 'Weasel\Annotation\TestResources\BlahFoo',
            )),
            array('\Weasel\Annotation\TestResources\NamespacedFirst', 'MultipleNamespace', array(
                '' => 'Weasel\Annotation\TestResources',
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                'TestB' => 'Weasel\Annotation\TestResources\TestB',
                'Test' => 'Weasel\Annotation\TestResources\BlahFoo',
            )),
            array('\Weasel\Annotation\TestResourcesToo\NamespacedSecond', 'MultipleNamespace', array(
                '' => 'Weasel\Annotation\TestResourcesToo',
                'TestC' => 'Weasel\Annotation\TestResources\TestC',
            )),
            array('\Weasel\Annotation\TestResources\NamespacedShorthand', 'NamespacedShorthand', array(
                '' => 'Weasel\Annotation\TestResources',
                'TestD' => 'Weasel\Annotation\TestResources\TestD',
                'TestE' => 'Weasel\Annotation\TestResources\TestE',
                'Test' => 'Weasel\Annotation\TestResources\TestF',
            )),
            array('NoNamespaceTest', 'NoNamespace', array(
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                '' => '',
            )),
            array('\Weasel\Annotation\TestResources\PHPGolfNamespace', 'PHPGolfNamespace', array(
                'TestA' => 'Weasel\Annotation\TestResources\TestA',
                '' => 'Weasel\Annotation\TestResources',
            )),
            array('\Weasel\Annotation\TestResourcesToo\PHPGolfNamespaceTwo', 'PHPGolfNamespace', array(
                'TestB' => 'Weasel\Annotation\TestResources\TestB',
                '' => 'Weasel\Annotation\TestResourcesToo',
            )),
        );
    }

    /**
     * @dataProvider provideClasses
     */
    public function testParse($class, $import, $expected)
    {
        /** @noinspection PhpIncludeInspection */
        require_once(__DIR__ . '/resources/' . $import . '.php');
        $rClass = new ReflectionClass($class);
        $parser = new PhpParser();
        $this->assertEquals($expected, $parser->parseClass($rClass));
    }

}

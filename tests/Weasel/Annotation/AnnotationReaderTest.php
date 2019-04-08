<?php
namespace Weasel\Annotation;

use Mockery as m;
use PHPUnit\Framework\TestCase;
use Weasel\Annotation\Config\Annotation;

class AnnotationReaderTest extends TestCase {


    /**
     * Test for the fix for issue #41
     */
    public function testCompilerLeftOverDocblock() {

        $mockConfigProvider = m::mock('\Weasel\Annotation\AnnotationConfigProvider');
        $mockConfigProvider->shouldReceive('get')->with('\HelloWorld')->andReturn(new Annotation('\stdClass', array(), 1));

        require __DIR__ . '/resources/AnnotatedClass.php';
        $reader = new AnnotationReader(new \ReflectionClass('\Weasel_Annotation_TestResources_AnnotatedClass'), $mockConfigProvider);
        $read = $reader->getClassAnnotations();

        // Ordering is important. We need to require the NoDocBlock file immediately to ensure that the last thing
        // parsed was the AnnotatedClass
        require __DIR__ . '/resources/NoDocBlock.php';
        $this->assertNotEmpty($read);

        $reader = new AnnotationReader(new \ReflectionClass('\Weasel_Annotation_TestResources_NoDocBlock'), $mockConfigProvider);

        $this->assertEmpty($reader->getClassAnnotations());

    }

}

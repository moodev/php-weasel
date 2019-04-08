<?php
/**
 * Created by IntelliJ IDEA.
 * User: jonathan
 * Date: 15/04/15
 * Time: 12:40
 */

namespace Weasel\DoctrineAnnotation {

    use Doctrine\Common\Annotations\AnnotationReader;
    use PHPUnit\Framework\TestCase;

    class DoctrineAnnotationReaderFactoryTest extends TestCase
    {

        /**
         * Test what the registered annotation autoloading does when faced with a sane use statement.
         * Must be run in a separate process to ensure the annotation hasn't already been loaded.
         * @runInSeparateProcess
         */
        public function testAutoloaderWithoutLeadingSlash()
        {
            $factory = new DoctrineAnnotationReaderFactory(new AnnotationReader());

            $rClass = new \ReflectionClass('\Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactoryTestA\Test');

            $reader = $factory->getReaderForClass($rClass);
            $reader->getClassAnnotations();
        }

        /**
         * Test what the registered annotation autoloading does when faced with a use with a leading \
         * Must be run in a separate process to ensure the annotation hasn't already been loaded.
         * @runInSeparateProcess
         */
        public function testAutoloaderWithLeadingSlash()
        {
            $factory = new DoctrineAnnotationReaderFactory(new AnnotationReader());

            $rClass = new \ReflectionClass('\Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactoryTestB\Test');

            $reader = $factory->getReaderForClass($rClass);
            $reader->getClassAnnotations();
        }


    }
}

namespace Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactoryTestA {
    use Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties;

    /**
     * Denotes that there was a problem while accessing the builder service.
     *
     * @JsonIgnoreProperties()
     */
    class Test
    {

    }
}

namespace Weasel\DoctrineAnnotation\DoctrineAnnotationReaderFactoryTestB {
    use \Weasel\JsonMarshaller\Config\DoctrineAnnotations\JsonIgnoreProperties;

    /**
     * Denotes that there was a problem while accessing the builder service.
     *
     * @JsonIgnoreProperties()
     */
    class Test
    {

    }
}
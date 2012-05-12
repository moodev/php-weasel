<?php
namespace PhpMarshaller\Annotations;

/**
 * Created by JetBrains PhpStorm.
 * User: User
 * Date: 12/05/12
 * Time: 18:09
 * To change this template use File | Settings | File Templates.
 */
class AnnotationReader
{

    /**
     * @var \ReflectionClass
     */
    protected $class;

    protected $annotations = array();
    protected $classAnnotations = null;
    protected $methodAnnotations = null;
    protected $propertyAnnotations = null;

    protected $propertyGetters = null;
    protected $propertySetters = null;

    public function __construct(\ReflectionClass $class) {
        $this->class = $class;
    }

    public function getClassAnnotations() {
        if (isset($this->classAnnotations)) {
            return $this->classAnnotations;
        }

        $docblock = $this->class->getDocComment();
        $this->_parse($docblock);

    }

    public function getClassAnnotation($annotation) {

    }

    public function getMethodAnnotations($method) {

    }

    public function getMethodAnnotation($method, $annotation) {

    }

    public function getPropertyAnnotations($property) {

    }

    public function getPropertyAnnotation($property, $annotation) {

    }

    public function getGetterForProperty($property) {

    }

    public function getSetterForProperty($property) {

    }
}

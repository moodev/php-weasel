<?php
namespace PhpAnnotation;

class AnnotationConfigurator
{

    public function get($name) {
        return array(
            'class' => '\PhpAnnotation\Annotations\Annotation',
            'on' => array('class'),
            'max' => 1,
            'creatorMethod' => '__construct',
            'creatorParams' => array(
                array(
                    'name' => 'on',
                    'type' => 'string[]',
                    'required' => false
                    ),
            )
        );
    }

}

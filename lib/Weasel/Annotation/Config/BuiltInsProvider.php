<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\Annotation\Config;

class BuiltInsProvider
{

    /**
     * @var AnnotationConfig
     */
    protected static $builtIns;

    protected static function _buildConfig()
    {

        self::$builtIns = new AnnotationConfig();

        $annotation = new Annotation('\Weasel\Annotation\Config\Annotations\Annotation', array('class'), 1);
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(
            new Param('on', 'string[]', false)
        );
        $annotation->addCreatorParam(
            new Param('max', 'integer', false)
        );
        self::$builtIns->addAnnotation($annotation);

        $annotation = new Annotation('\Weasel\Annotation\Config\Annotations\AnnotationCreator', array('method'), 1);
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(
            new Param('params', '\Weasel\Annotation\Config\Annotations\Parameter[]', false)
        );
        self::$builtIns->addAnnotation($annotation);

        $annotation =
            new Annotation('\Weasel\Annotation\Config\Annotations\Parameter', array('\Weasel\Annotation\Config\Annotations\AnnotationCreator'), null);
        $annotation->setCreatorMethod('__construct');
        $annotation->addCreatorParam(
            new Param('name', 'string', false)
        );
        $annotation->addCreatorParam(
            new Param('type', 'string', false)
        );
        $annotation->addCreatorParam(
            new Param('required', 'bool', false)
        );
        self::$builtIns->addAnnotation($annotation);
    }

    public static function getConfig()
    {
        if (!isset(self::$builtIns)) {
            self::_buildConfig();
        }
        return self::$builtIns;
    }

}

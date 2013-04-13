<?php
namespace Weasel;

use Weasel\Annotation\AnnotationConfigurator;
use Weasel\JsonMarshaller\JsonMapper;
use Weasel\XmlMarshaller\XmlMapper;

/**
 * Class WeaselFactory
 * @package Weasel
 *
 * Interface to be implemented by factories capable of producing mapper instances.
 * The instances returned are expected to be singletons.
 */
interface WeaselFactory
{

    /**
     * Get a fully configured XmlMapper instance.
     * @return XmlMapper
     */
    public function getXmlMapperInstance();

    /**
     * Get a fully configured JsonMapper instance.
     * @return JsonMapper
     */
    public function getJsonMapperInstance();

}

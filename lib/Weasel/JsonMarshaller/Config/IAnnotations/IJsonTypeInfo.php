<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 */
namespace Weasel\JsonMarshaller\Config\IAnnotations;

/**
 * Configure behaviour when dealing with inheritance.
 * When on a class this defines the behaviour to use when dealing with it's subclasses.
 * When on a property this defines the behaviour to use when dealing with subclasses of a the property's type.
 *
 * Without this we wont be able to work out how to encode the type of subclasses.
 */
interface IJsonTypeInfo
{

    /*
     * These constants define what to use as the identifier of a subclass
     */
    const ID_CLASS = 1; // Use the fully qualified class name (default.)
    const ID_CUSTOM = 2; // Use a custom method to obtain the name.
    const ID_MINIMAL_CLASS = 3; // Use the class name (not including namespace.)
    const ID_NAME = 4; // Use a "name" configured by @JsonSubTypes (on the parent) or @JsonTypeName (on the child)
    const ID_NONE = 5; // Don't store anything. This will not permit deserialization as a subclass.

    /*
     * And these constants set where to store the identifier.
     */
    const AS_PROPERTY = 1; // Store as a property on the JSON object (default.)
    const AS_WRAPPER_ARRAY = 2; // Store as a wrapper array e.g. array($id, $object)
    const AS_WRAPPER_OBJECT = 3; // Store as a wrapper object e.g. array($id => $object)
    const AS_EXTERNAL_PROPERTY = 4; // Store as a property on the containing object (only works if on a property.)

    /**
     * @return int Get how to include the ID, one of the AS_ constants.
     */
    public function getInclude();

    /**
     * @return string If we're storing as property or ext property, what name should we use? (default "@type")
     */
    public function getProperty();

    /**
     * @return int Get what to use as the ID, one of the ID_ constants.
     */
    public function getUse();

    /**
     * @return bool If we're storing as a property should the property be visible on the PHP object?
     */
    public function getVisible();

    /**
     * @return string If we can't work out what subclass to use, we'll assume it's one of these. Fully qualified class.
     */
    public function getDefaultImpl();

}

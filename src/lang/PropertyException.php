<?php namespace ovide\libs\lang;

use LogicException;

/**
 * Implements a exception thrown when tries to access to a non-accessible
 * property for an AutoProp object.
 * @author Albert Ovide <albert@ovide.net>
 * @version 1.0
 */
class PropertyException extends LogicException
{
    const READ_ACESS   = 'Read';
    const WRITE_ACCESS = 'Write';


    /**
     * Constructor for PropertyException
     * @param string $propName The property name
     * @param string $class Class name
     * @param string $access The denied access type 'Read'|'Write'
     */
    public function __construct($propName, $class, $access)
    {
        $msg = "$access property $propName not found at $class";
        parent::__construct($msg, 0, null);
    }
}

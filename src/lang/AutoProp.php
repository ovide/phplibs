<?php namespace ovide\libs\lang;

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

/**
 * This class improves its inherited ones, generating automatically properties
 * following a convention according to its attributes, getters and setters.
 *
 * The convention follows these rules:
 *
 * -  public and protected attributes have its read-write property
 * -  private attributes haven't propery
 * -  attributes starting with _ (underscore) have their read-only property
 * -  attributes starting with __ (double underscore) haven't property
 * -  Methods starting with getXXX generate a read property for attribute XXX,
 *    regardless of the above rules.
 * -  Methods starting with setXXX generate a write property for attribute XXX,
 *    regardless of the above rules.
 * -  All properties starts with uppercase. This means, for example, setValue()
 *    will generate a write property 'Value' for $value (or $Value) attribute.
 *    getValue() will generate a read property 'Value'
 *    for $value (or $_value, or $_Value)
 * -  Therefore, you can't create two attributes which may collide names like:
 *    $value, $Value, $_value or $_Value
 * @author Albert Ovide <albert@ovide.net>
 * @version 1.0
 */
abstract class AutoProp
{

    /**
     * Instanced ReflectionClass for the current object
     * @var ReflectionClass
     */
    private $reflection = null;
    /**
     * List of available setters and getters
     * @var string[]
     */
    private $methods = array();
    /**
     * List of available attributes.
     *   This is all public and protected attributes
     * wich don't start with double underscore '__'
     * @var string[]
     */
    private $attrs = array();

    /**
     * Read properties
     * @param string $Name
     * @return mixed
     * @throws PropertyException If property doesn't exist
     */
    final public function __get($Name)
    {
        //assuming $Name='Name';
        if ($this->reflection==null) $this->initReflection();
        //check for getName()
        $getter = "get$Name";
        if (in_array($getter, $this->methods)) return $this->$getter();
        //check for $Name,$_Name,$name,$_name
        $lc = lcfirst($Name);
        $checks = array($Name,"_$Name",$lc,"_$lc");
        foreach ($checks as $check)
            if (in_array($check,$this->attrs)) return $this->$check;
        //Property not found!
        $cn = get_class($this);
        throw new PropertyException($Name, $cn, PropertyException::READ_ACESS);
    }

    /**
     * Write properties
     * @param string $Name
     * @param mixed $value
     * @return mixed
     * @throws PropertyException
     */
    final public function __set($Name, $value)
    {
        //assuming $Name='Name'
        if ($this->reflection == null) $this->initReflection();
        //check for setName()
        $setter = "set$Name";
        if (in_array($setter, $this->methods)) {
            $this->$setter($value);
            return;
        }
        //check for $Name,$name
        $lc = lcfirst($Name);
        $checks = array($Name,$lc);

        foreach ($checks as $check)
            if (in_array($check, $this->attrs)) {
                $this->$check=$value;
                return;
            }
        //Property not found!
        $cn = get_class($this);
        throw new PropertyException($Name, $cn, PropertyException::WRITE_ACCESS);
    }

    /**
     * Init. reflection vars
     * @codeCoverageIgnore
     */
    private function initReflection()
    {
        $reflection = new ReflectionClass(get_class($this));
        $methods = $reflection->getMethods(
                ReflectionMethod::IS_PUBLIC |
                ReflectionMethod::IS_PROTECTED);
        foreach ($methods as $method) {
            if ((substr($method->getName(), 0, 3) == 'get') ||
                (substr($method->getName(), 0, 3) == 'set'))
                    $this->methods[]=$method->getName();
        }
        $attrs = $reflection->getProperties(
                ReflectionProperty::IS_PROTECTED |
                ReflectionProperty::IS_PUBLIC);
        foreach ($attrs as $attr) {
            if (substr($attr->getName(), 0, 2) != '__')
                    $this->attrs[] = $attr->getName();
        }
        $this->reflection = $reflection;
    }
}

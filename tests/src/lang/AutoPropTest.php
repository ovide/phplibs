<?php namespace ovide\libs\lang;

use Exception;

/**
 * @todo Test fatal errors
 */
class AutoPropTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dummy
     */
    protected $o;

    protected function setUp()
    {
        $this->o = new Dummy();
    }

    public function testProtectedSimple()
    {
        $this->assertEquals('protected 1', $this->o->ProtectedSimpleVar);
        $this->o->ProtectedSimpleVar = 'protected 11';
        $this->assertEquals('protected 11', $this->o->ProtectedSimpleVar);
    }

    public function testPrivateWithGetter()
    {
        $this->assertEquals('foo', $this->o->PrivateWithGetter);
        try{
            $foo = $this->o->PrivateWithGetter = 'exception';
        }catch(Exception $e){
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Write property PrivateWithGetter not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
    }

    public function testPrivateWithSetter()
    {
        $this->o->PrivateWithSetterGetter = 1;
        $this->assertEquals(6, $this->o->PrivateWithSetterGetter);
    }

    public function testProtectedUnderscore()
    {
        $this->assertEquals('under_', $this->o->ProtectedUnderscore);
        try {
            $this->o->ProtectedUnderscore = 'exception';
        } catch (Exception $e) {
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Write property ProtectedUnderscore not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
    }
    
    public function testPublicWithGetter()
    {
        $this->o->publicWithGetter = 'foo';
        $this->assertEquals('foo', $this->o->publicWithGetter);
        $this->assertEquals('foo var', $this->o->PublicWithGetter);
    }

    public function testNotFound()
    {
        try {
            $foo = $this->o->NotFound;
        } catch(Exception $e) {
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Read property NotFound not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }        
    }
    
    public function testPrivateSimple()
    {
        try {
            $this->o->PrivateSimple = 'exception';
        } catch (Exception $e){
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Write property PrivateSimple not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
        try {
            $var = $this->o->PrivateSimple;
        } catch(Exception $e) {
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Read property PrivateSimple not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
    }
    
    public function testDoubleUnderscore()
    {
        try {
            $var = $this->o->DoubleUnderscore;
        } catch (Exception $e) {
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Read property DoubleUnderscore not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
        try {
            $this->o->DoubleUnderscore = 'exception';
        } catch (Exception $e) {
            $this->assertTrue($e instanceof PropertyException);
            $expected = 'Write property DoubleUnderscore not found at ovide\libs\lang\Dummy';
            $this->assertEquals($expected, $e->getMessage());
        }
    }
}

/**
 * @property mixed $ProtectedSimpleVar
 * @property-read mixed $PrivateWithGetter
 * @property int $PrivateWithSetterGetter
 * @property-read mixed $ProtectedUnderscore
 * @property-read mixed $PublicWithGetter
 */
class Dummy extends AutoProp
{
    private $privateSimple;
    protected $protectedSimpleVar;

    private $privateWithGetter;
    private $privateWithSetterGetter;
    protected $_protectedUnderscore;
    protected $__doubleUnderscore;
    public $publicWithGetter;

    public function __construct()
    {
        $this->privateSimple = 'fixed value';
        $this->protectedSimpleVar = 'protected 1';
        $this->privateWithGetter  = 'foo';
        $this->_protectedUnderscore = 'under_';
        $this->__doubleUnderscore = 'double_';

    }

    public function getPrivateWithGetter()
    {
        return $this->privateWithGetter;
    }
    
    public function getPublicWithGetter()
    {
        return $this->publicWithGetter.' var';
    }
    
    public function getPrivateWithSetterGetter()
    {
        return $this->privateWithSetterGetter;
    }
    
    public function setPrivateWithSetterGetter($value)
    {
        $this->privateWithSetterGetter = $value + 5;
    }
}

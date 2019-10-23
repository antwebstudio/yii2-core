<?php  
namespace ant\traits;

use Yii;
use yii\base\InvalidCallException;
use yii\base\UnknownPropertyException;

trait GetterSetterTrait
{
    public function __isset($name) {
        return $this->{$name} != null;
    }

    public function __get($name)
    {
        try {

            return parent::__get($name);

        } catch (\Exception $ex) {

            return $this->getterOverrideHandler($name, $ex);

        }
    }

    public function __set($name, $value)
    {
        try {
            
            parent::__set($name, $value);


        } catch (\Exception $ex) {
            
            return $this->setterOverrideHandler($name, $value, $ex);

        }
    }

    private function getterOverrideHandler($name, $ex)
    {
    	$this->validateCallerClass();

    	return $this->getterOverride($name, $ex);
    }

    private function setterOverrideHandler($name, $value, $ex)
    {
    	$this->validateCallerClass();

    	return $this->setterOverride($name, $value, $ex);
    }

    private function validateCallerClass()
    {
        $implementClass = 'ant\interfaces\GetterSetterTraitInterface';

        $classImplements = class_implements(get_called_class());

        if (!isset($classImplements[$implementClass])) 
        {
            throw new \Exception(get_called_class() . ' must implement interface "' . $implementClass . '"' , 1);
        }
    }
}
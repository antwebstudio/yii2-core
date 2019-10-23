<?php  
namespace ant\interfaces;

interface GetterSetterTraitInterface
{
	public function getterOverride($name, $ex);

	public function setterOverride($name, $value, $ex);
}
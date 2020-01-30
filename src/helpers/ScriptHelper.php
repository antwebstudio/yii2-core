<?php  
namespace ant\helpers;

class ScriptHelper
{
	public static function jsOneLineString($string)
	{
		return trim(addslashes(preg_replace( "/\r|\n/", "", $string)));
	}

	public static function identifierSanitizer($string)
	{
    	return preg_replace('/[^a-zA-Z0-9_]/', '_', (string) $string);
	}

}
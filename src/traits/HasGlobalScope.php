<?php
namespace ant\traits;

trait HasGlobalScope {
	protected static $globalScopes = [];
	protected static $attachByDefault = true;
	
	public static function attachGlobalScope($name) {
		static::$globalScopes[$name] = true;
	}
	
	public static function detachGlobalScope($name) {
		static::$globalScopes[$name] = false;
	}
	
	public static function hasGlobalScope($name) {
		return static::$globalScopes[$name] ?? true;
	}
}
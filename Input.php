<?php

class Input
{
	public static function has($key) {
		if (isset($_REQUEST[$key])) {
			return true;
		} else {
			return false;
		}
	}

	public static function get($key, $default = null)
	{
		$value = $_REQUEST[$key];
		return $value;
	}

	// prevents class from being instantiated
	private function __construct() {}
}
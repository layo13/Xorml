<?php

/**
 * Autoloads Xorml classes.
 *
 * @author Lionel Guissani
 */
class XormlAutoloader {

	/**
	 * Registers XormlAutoloader as an SPL autoloader.
	 */
	public static function register() {
		spl_autoload_register(array(__CLASS__, 'autoload'));
	}

	/**
	 * Handles autoloading of classes.
	 *
	 * @param string $class A class name.
	 */
	public static function autoload($class) {
		if (0 == strpos($class, 'Xorml')) {
			return;
		}

		if (is_file($file = dirname(__FILE__) . '/./' . str_replace('\\', '/', $class) . '.php')) {
			
			require $file;
		}
	}

}

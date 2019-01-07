<?php
/**
 * FooBar extension hooks
 *
 * @file
 * @ingroup Extensions
 */
class FooBarHooks {
	/**
	 * Conditionally register the unit testing module for the ext.fooBar module
	 * only if that module is loaded
	 *
	 * @param array $testModules The array of registered test modules
	 * @param ResourceLoader $resourceLoader The reference to the resource loader
	 * @return true
	 */
	public static function onResourceLoaderTestModules( array &$testModules, ResourceLoader &$resourceLoader ) {
		$testModules['qunit']['ext.fooBar.tests'] = [
			'scripts' => [
				'tests/FooBar.test.js'
			],
			'dependencies' => [
				'ext.fooBar'
			],
			'localBasePath' => __DIR__,
			'remoteExtPath' => 'FooBar',
		];
		return true;
	}
}

<?php

use MWStew\Builder\Structure;
use MWStew\Builder\ExtensionDetails;
use PHPUnit\Framework\TestCase;

class StructureTest extends TestCase {
	public function testFileMap() {
		$cases = [
			[
				'data' => [ 'name' => 'testName' ],
				'msg' => 'Name only',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json'
				]
			],
			[
				'data' => [ 'name' => 'testName', 'license' => 'MIT' ],
				'msg' => 'Valid license (MIT)',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'COPYING',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'license' => 'woop' ],
				'msg' => 'Invalid license (MIT); no license file produced',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'specialpage_name' => 'Foobar' ],
				'msg' => 'Special page',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'specials/SpecialFoobar.php',
					'testName.alias.php',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'dev_js' => true ],
				'msg' => 'JS development environment',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'.eslintrc.json',
					'.stylelintrc',
					'Gruntfile.js',
					'package.json',
					'modules/ext.testName.js',
					'modules/ext.testName.css',
					'tests/testName.test.js',
					'tests/.eslintrc.json',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'dev_php' => true ],
				'msg' => 'PHP development environment',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'composer.json',
					'tests/testName.test.php',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'dev_js' => true, 'dev_php' => true ],
				'msg' => 'PHP and JS development environments',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'.eslintrc.json',
					'.stylelintrc',
					'Gruntfile.js',
					'package.json',
					'modules/ext.testName.js',
					'modules/ext.testName.css',
					'tests/testName.test.js',
					'tests/.eslintrc.json',
					'composer.json',
					'tests/testName.test.php',
				]
			],
			[
				'data' => [
					'name' => 'testName',
					'dev_js' => true,
					'dev_php' => true,
					'specialpage_name' => 'Foobar',
					'specialpage_title' => 'The FooBar Page',
				],
				'msg' => 'PHP and JS development environments with a special page',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'.eslintrc.json',
					'.stylelintrc',
					'Gruntfile.js',
					'package.json',
					'modules/ext.testName.js',
					'modules/ext.testName.css',
					'tests/testName.test.js',
					'tests/.eslintrc.json',
					'composer.json',
					'tests/testName.test.php',
					'specials/SpecialFoobar.php',
					'testName.alias.php'
				]
			],
		];

		foreach ( $cases as $testCase ) {
			// Only used to produce twig-ready params
			$details = new ExtensionDetails( $testCase[ 'data' ] );
			$params = $details->getAllParams();
			$this->assertEquals(
				$testCase[ 'expected' ],
				array_keys( Structure::getFileMap( $params ) ),
				$testCase[ 'msg' ]
			);
		}
	}

}

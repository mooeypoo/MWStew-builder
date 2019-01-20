<?php

use MWStew\Builder\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase {
	public function testGeneratorTools() {
		$obj = [
			'one' => 'two',
			'deeper' => [
				'three' => 'four',
				'deeperStill' => [
					'onemore' => 'level',
					'withBool' => true,
					'withNumber' => 15,
				]
			]
		];

		$this->assertEquals(
			'two',
			Generator::getObjectProp( $obj, [ 'one' ] ),
			'Straight forward highest key value'
		);

		$this->assertEquals(
			'four',
			Generator::getObjectProp( $obj, [ 'deeper', 'three' ] ),
			'Second level existing value'
		);

		$this->assertEquals(
			'level',
			Generator::getObjectProp( $obj, [ 'deeper', 'deeperStill', 'onemore' ] ),
			'Third level existing value'
		);

		$this->assertEquals(
			true,
			Generator::getObjectProp( $obj, [ 'deeper', 'deeperStill', 'withBool' ] ),
			'Deep level boolean'
		);

		$this->assertEquals(
			15,
			Generator::getObjectProp( $obj, [ 'deeper', 'deeperStill', 'withNumber' ] ),
			'Deep level number'
		);

		$this->assertNull(
			Generator::getObjectProp( $obj, [ 'simpleNonexistent' ] ),
			'Nonexisting in highest key value'
		);

		$this->assertNull(
			Generator::getObjectProp( $obj, [ 'deeper', 'deeperStill', 'nonexistent' ] ),
			'Nonexisting in deeper key value'
		);

		$this->assertNull(
			Generator::getObjectProp( $obj, [ 'deeper', 'non', 'existent', 'keys' ] ),
			'Nonexisting set of keys'
		);
	}
	public function testFileList() {
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
					'testNameHooks.php',
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
					'testNameHooks.php',
				]
			],
			[
				'data' => [ 'name' => 'testName', 'hooks' => [ 'foo::bar' ] ],
				'msg' => 'Add hooks',
				'expected' => [
					'extension.json',
					'CODE_OF_CONDUCT.md',
					'i18n/en.json',
					'i18n/qqq.json',
					'testNameHooks.php',
				]
			],
		];

		foreach ( $cases as $testCase ) {
			$generator = new Generator( $testCase['data'] );
			$files = $generator->getFiles();

			$this->assertEquals(
				$testCase[ 'expected' ],
				array_keys( $files ),
				$testCase[ 'msg' ]
			);
		}
	}

	public function testFileContents() {
		$cases = [
			[
				'data' => [ 'name' => 'testName' ],
				'msg' => 'Giving name only',
				'config' => [],
				'expectedFileCount' => 4,
				'expectedFileContent' => [
					'extension.json' => 'nameonly.extension.json',
					'i18n/en.json' => 'nameonly.i18n.en.json',
					'i18n/qqq.json' => 'nameonly.i18n.qqq.json',
				],
			],
			[
				'data' => [ 'name' => 'testName', 'url' => 'http://www.demo.com/testURL' ],
				'msg' => 'Giving name and URL',
				'config' => [],
				'expectedFileCount' => 4,
				'expectedFileContent' => [
					'extension.json' => 'nameurl.extension.json',
					'i18n/en.json' => 'nameurl.i18n.en.json',
					'i18n/qqq.json' => 'nameurl.i18n.qqq.json',
				],
			],
			[
				'data' => [ 'name' => 'secondTest', 'title' => 'Some random extension' ],
				'msg' => 'Giving name and display name',
				'config' => [],
				'expectedFileCount' => 4,
				'expectedFileContent' => [
					'extension.json' => 'nametitle.extension.json',
					'i18n/en.json' => 'nametitle.i18n.en.json',
					'i18n/qqq.json' => 'nametitle.i18n.qqq.json',
				],
			],
			[
				'data' => [
					'name' => 'thirdTest',
					'title' => 'Another random extension',
					'description' => 'A description for the random extension',
					'author' => 'Moe Schmoe',
				],
				'msg' => 'Supplying name, title, description, author',
				'config' => [],
				'expectedFileCount' => 4,
				'expectedFileContent' => [
					'extension.json' => 'nametitledescauthor.extension.json',
					'i18n/en.json' => 'nametitledescauthor.i18n.en.json',
					'i18n/qqq.json' => 'nametitledescauthor.i18n.qqq.json',
				],
			],
			[
				'data' => [
					'name' => 'thirdTest',
					'title' => 'Another random extension',
					'description' => 'A description for the random extension',
					'author' => 'Moe Schmoe',
					'license' => 'MIT',
				],
				'msg' => 'Supplying a license adds LICENSE file',
				'config' => [],
				// We will not check the contents of the license file,
				// but we can make sure that there are 4 files instead of just 3
				'expectedFileCount' => 5,
				'expectedFileContent' => [
					'extension.json' => 'nametitledescauthorlicense.extension.json',
					'i18n/en.json' => 'nametitledescauthorlicense.i18n.en.json',
					'i18n/qqq.json' => 'nametitledescauthorlicense.i18n.qqq.json',
				],
			],
			[
				'data' => [ 'name' => 'FooBar', 'dev_js' => true ],
				'msg' => 'Javascript environment',
				'config' => [],
				'expectedFileCount' => 13,
				'expectedFileContent' => [
					// Skipping the comparison to files that are static and do note
					// depend on template parameters.
					// 'CODE_OF_CONDUCT.md',
					// '.eslintrc.json',
					// '.stylelintrc',
					// 'Gruntfile.js',
					// 'package.json',
					// 'tests/.eslintrc.json',
					'FooBarHooks.php' => 'jsenv.Hooks.php',
					'extension.json' => 'jsenv.extension.json',
					'modules/ext.fooBar.js' => 'jsenv.module.js',
					'modules/ext.fooBar.css' => 'jsenv.module.css',
					'tests/FooBar.test.js' => 'jsenv.tests.qunit.js',
					'i18n/en.json' => 'jsenv.i18n.en.json',
					'i18n/qqq.json' => 'jsenv.i18n.qqq.json',
				],
			],
			[
				'data' => [
					'name' => 'FooBar',
					'specialpage_name' => 'FooBarPage',
					'specialpage_title' => 'This is the FooBar page',
					'specialpage_intro' => 'Introduction to Foobar'
				],
				'msg' => 'Special page',
				'config' => [],
				'expectedFileCount' => 6,
				'expectedFileContent' => [
					'extension.json' => 'specialpage.extension.json',
					'i18n/en.json' => 'specialpage.i18n.en.json',
					'i18n/qqq.json' => 'specialpage.i18n.qqq.json',
				],
			],
		];
		foreach ( $cases as $testCase ) {
			$generator = new Generator( $testCase['data'], $testCase['config'] );
			$files = $generator->getFiles();

			$this->assertEquals(
				$testCase['expectedFileCount'],
				count( array_keys( $files ) ),
				$testCase['msg'] . ': Number of files'
			);
			foreach ( $testCase['expectedFileContent'] as $fName => $fContent ) {
				$expected = file_get_contents( __DIR__ . '/data/' . $fContent );

				$this->assertEquals(
					$expected,
					$files[ $fName ],
					$testCase['msg'] . ': File structure for ' . $fName
				);
			}
		}
	}

	private function getArrayExtendedCopy( $arr, $extendValues = [] ) {
		$arrObj = new ArrayObject( $arr );
		$newArray = $arrObj->getArrayCopy();

		return array_merge( $newArray, $extendValues );
	}
}

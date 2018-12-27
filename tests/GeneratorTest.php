<?php

use MWStew\Builder\Generator;
use PHPUnit\Framework\TestCase;

class GeneratorTest extends TestCase {
	private $baseFileStructure = [
		'extension.json' => [
			'name' => '',
			'version' => '0.0.0',
			'namemsg' => '',
			'descriptionmsg' => '',
			'type' => 'other',
			'manifest_version' => 1,
			'MessagesDirs' => [],
			'AutoloadClasses' => []
		],
		'i18n/en.json' => [
			'@metadata' => [ 'authors' => [] ]
		],
		'i18n/qqq.json' => [
			'@metadata' => [ 'authors' => [] ],
		],
	];

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

	public function testFiles() {
		$cases = [
			[
				'data' => [ 'name' => 'testName' ],
				'msg' => 'Giving name only',
				'config' => [],
				'expectedFileCount' => 3,
				'expectedFiles' => [
					'extension.json' => [
						'name' => 'testName',
						'namemsg' => 'testName',
						'descriptionmsg' => 'testName-desc',
						'MessagesDirs' => [
							'testName' => [ 'i18n' ]
						]
					],
					'i18n/en.json' => [
						'testName' => 'testName',
						'testName-desc' => '',
					],
					'i18n/qqq.json' => [
						'testName' => 'The name of the extension',
						'testName-desc' => '{{desc|name=testName|url=}}',
					],
				],
			],
			[
				'data' => [ 'name' => 'secondTest', 'title' => 'Some random extension' ],
				'msg' => 'Giving name and display name',
				'config' => [],
				'expectedFileCount' => 3,
				'expectedFiles' => [
					'extension.json' => [
						'name' => 'secondTest',
						'namemsg' => 'secondTest',
						'descriptionmsg' => 'secondTest-desc',
						'MessagesDirs' => [
							'secondTest' => [ 'i18n' ]
						]
					],
					'i18n/en.json' => [
						'secondTest' => 'Some random extension',
						'secondTest-desc' => '',
					],
					'i18n/qqq.json' => [
						'secondTest' => 'The name of the extension',
						'secondTest-desc' => '{{desc|name=secondTest|url=}}',
					],
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
				'expectedFileCount' => 3,
				'expectedFiles' => [
					'extension.json' => [
						'name' => 'thirdTest',
						'namemsg' => 'thirdTest',
						'author' => [ 'Moe Schmoe' ],
						'descriptionmsg' => 'thirdTest-desc',
						'MessagesDirs' => [
							'thirdTest' => [ 'i18n' ]
						]
					],
					'i18n/en.json' => [
						'@metadata' => [ 'authors' => [ 'Moe Schmoe' ] ],
						'thirdTest' => 'Another random extension',
						'thirdTest-desc' => 'A description for the random extension',
					],
					'i18n/qqq.json' => [
						'@metadata' => [ 'authors' => [ 'Moe Schmoe' ] ],
						'thirdTest' => 'The name of the extension',
						'thirdTest-desc' => '{{desc|name=thirdTest|url=}}',
					],
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
				'expectedFileCount' => 4,
				'expectedFiles' => [
					'extension.json' => [
						'name' => 'thirdTest',
						'namemsg' => 'thirdTest',
						'author' => [ 'Moe Schmoe' ],
						'license-name' => 'MIT',
						'descriptionmsg' => 'thirdTest-desc',
						'MessagesDirs' => [
							'thirdTest' => [ 'i18n' ]
						]
					],
					'i18n/en.json' => [
						'@metadata' => [ 'authors' => [ 'Moe Schmoe' ] ],
						'thirdTest' => 'Another random extension',
						'thirdTest-desc' => 'A description for the random extension',
					],
					'i18n/qqq.json' => [
						'@metadata' => [ 'authors' => [ 'Moe Schmoe' ] ],
						'thirdTest' => 'The name of the extension',
						'thirdTest-desc' => '{{desc|name=thirdTest|url=}}',
					],
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

			foreach ( $testCase['expectedFiles'] as $fName => $fContent ) {
				$this->assertEquals(
					json_encode(
						$this->getArrayExtendedCopy(
							$this->baseFileStructure[$fName],
							$fContent
						),
						JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE
					),
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

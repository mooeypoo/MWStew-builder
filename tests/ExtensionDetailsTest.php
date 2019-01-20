<?php

use MWStew\Builder\ExtensionDetails;
use PHPUnit\Framework\TestCase;

class ExtensionDetailsTest extends TestCase {
	public function testGetters() {
		$data = [
			'name' => 'FooBar',
			'title' => 'Foo bar',
			'author' => 'Moe Schmoe',
			'version' => '1.2.3',
			'description' => 'A test description',
			'url' => 'http://www.example.com/foobar',
			'license' => 'MIT',
			'dev_php' => true,
			'dev_js' => true,
			'specialpage_name' => 'Foobar',
			'specialpage_title' => 'Get the foobar',
			'specialpage_intro' => 'A great new special foobar page.',
			'hooks' => [ 'foo', 'bar', 'baz::with:something' ]
		];

		$details = new ExtensionDetails( $data );

		$this->assertEquals( 'FooBar', $details->getName() );
		$this->assertEquals( 'fooBar', $details->getLowerCamelName() );
		$this->assertEquals( 'FooBar', $details->getClassName() );
		$this->assertEquals( true, $details->isEnvironment( 'js' ) );
		$this->assertEquals( true, $details->isEnvironment( 'php' ) );
		$this->assertEquals( 'MIT', $details->getLicense() );
		$this->assertEquals( true, $details->hasSpecialPage() );
		$this->assertEquals( 'SpecialFoobar', $details->getSpecialPageClassName() );
		$this->assertEquals( 'special-foobar', $details->getSpecialPageKeyFormat() );
		$this->assertEquals( [ 'foo', 'bar', 'baz::with:something' ], $details->getHooks() );
	}

	public function testGetTemplateParams() {
		$baseValues = [
			'title' => '',
			'author' => '',
			'version' => '0.0.0',
			'license' => '',
			'desc' => '',
			'url' => '',
			'parts' => [
				'javascript' => false,
				'php' => false,
			],
			'specialpage' => [
				'exists' => false,
				'name' => [
					'name' => '',
					'lowerCamelName' => '',
					'i18n' => '',
				],
				'className' => '',
				'title' => '',
				'intro' => '',
			],
			'hooksReference' => [],
			'hookMethods' => [],
		];
		$cases = [
			[
				'msg' => 'Name only',
				'input' => [
					'name' => 'FooBar',
				],
				'expected' => [
					'name' => 'FooBar',
					'title' => 'FooBar',
					'lowerCamelName' => 'fooBar'

				]
			],
			[
				'msg' => 'Non-numeric version format',
				'input' => [
					'name' => 'FooBar',
					'version' => 'v1-alpha'
				],
				'expected' => [
					'name' => 'FooBar',
					'title' => 'FooBar',
					'lowerCamelName' => 'fooBar',
					'version' => 'v1-alpha',
				]
			],
			[
				'msg' => 'Full data but no extras',
				'input' => [
					'name' => 'BarBaz',
					'title' => 'A bar baz extension',
					'author' => 'Moe Schmoe',
					'version' => '1.2.3',
					'description' => 'A test description',
					'url' => 'http://www.example.com/barbaz',
					'license' => 'GPL-2.0+',
				],
				'expected' => [
					'name' => 'BarBaz',
					'lowerCamelName' => 'barBaz',
					'title' => 'A bar baz extension',
					'author' => 'Moe Schmoe',
					'version' => '1.2.3',
					'license' => 'GPL-2.0+',
					'desc' => 'A test description',
					'url' => 'http://www.example.com/barbaz',
				],
			],
			[
				'msg' => 'PHP environment',
				'input' => [
					'name' => 'BarBaz',
					'dev_php' => true
				],
				'expected' => [
					'name' => 'BarBaz',
					'lowerCamelName' => 'barBaz',
					'title' => 'BarBaz',
					'parts' => [
						'php' => true,
						'javascript' => false,
					],
				],
			],
			[
				'msg' => 'JS environment',
				'input' => [
					'name' => 'BarBaz',
					'dev_js' => true
				],
				'expected' => [
					'name' => 'BarBaz',
					'lowerCamelName' => 'barBaz',
					'title' => 'BarBaz',
					'parts' => [
						'php' => false,
						'javascript' => true,
					],
					'hooksReference' => [
						'"ResourceLoaderTestModules": [ "BarBazHooks::onResourceLoaderTestModules" ]',
					]
				],
			],
			[
				'msg' => 'Special page',
				'input' => [
					'name' => 'BarBaz',
					'specialpage_name' => 'BarPage',
					'specialpage_title' => 'Bar Page',
					'specialpage_intro' => 'The place for bars',
				],
				'expected' => [
					'name' => 'BarBaz',
					'lowerCamelName' => 'barBaz',
					'title' => 'BarBaz',
					'specialpage' => array(
						'exists' => true,
						'name' => array(
							'name' => 'BarPage',
							'lowerCamelName' => 'barPage',
							'i18n' => 'special-barPage',
						),
						'className' => 'SpecialBarPage',
						'title' => 'Bar Page',
						'intro' => 'The place for bars',
					),
				],
			],
			[
				'msg' => 'Hooks',
				'input' => [
					'name' => 'BarBazThing',
					'title' => 'Bar Baz Thing',
					'hooks' => [ 'unrecognized::hook:thing', 'ArticleDeleteComplete' ]
				],
				'expected' => [
					'name' => 'BarBazThing',
					'lowerCamelName' => 'barBazThing',
					'title' => 'Bar Baz Thing',
					'hooksReference' => [
						'"unrecognized::hook:thing": [ "BarBazThingHooks::onUnrecognizedHookThing" ]',
						'"ArticleDeleteComplete": [ "BarBazThingHooks::onArticleDeleteComplete" ]'
					],
					// We'll test hook methods output in another test
					// 'hookMethods' => []
				],
			],
			[
				'msg' => 'Hooks and JS environment',
				'input' => [
					'name' => 'BarBazThing',
					'title' => 'Bar Baz Thing',
					'dev_js' => true,
					'hooks' => [ 'unrecognized::hook:thing', 'ArticleDeleteComplete' ],
				],
				'expected' => [
					'name' => 'BarBazThing',
					'lowerCamelName' => 'barBazThing',
					'title' => 'Bar Baz Thing',
					'parts' => [
						'php' => false,
						'javascript' => true,
					],
					'hooksReference' => [
						'"unrecognized::hook:thing": [ "BarBazThingHooks::onUnrecognizedHookThing" ]',
						'"ArticleDeleteComplete": [ "BarBazThingHooks::onArticleDeleteComplete" ]',
						'"ResourceLoaderTestModules": [ "BarBazThingHooks::onResourceLoaderTestModules" ]',
					]
				],
			],
		];

		foreach ( $cases as $testCase  ) {
			$details = new ExtensionDetails( $testCase['input'] );
			$expected = array_merge( $baseValues, $testCase['expected'] );
			$result = $details->getTemplateParams();

			// HACK: Remove 'hookMethods' because it's too big to test
			// in this suite; It is tested as part of the result of
			// the GeneratorTest
			unset( $expected['hookMethods'] );
			unset( $result['hookMethods'] );

			$this->assertEquals(
				$expected,
				$result,
				$testCase['msg']
			);
		}
	}
}

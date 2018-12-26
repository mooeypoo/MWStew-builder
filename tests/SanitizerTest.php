<?php

use MWStew\Builder\Sanitizer;
use PHPUnit\Framework\TestCase;

class SanitizerTest extends TestCase {

	public function testSanitizerParams() {
		$params = array(
			'text' => 'response',
			'array' => array( 1, 2, 3 ),
			'object' => array( 'one' => 'two' ),
		);

		$sanitizer = new Sanitizer( $params );

		$this->assertEquals(
			'response',
			$sanitizer->getParam( 'text' )
		);
		$this->assertEquals(
			array( 1, 2, 3 ),
			$sanitizer->getParam( 'array' )
		);
		$this->assertEquals(
			array( 'one' => 'two' ),
			$sanitizer->getParam( 'object' )
		);
		$this->assertNull(
			$sanitizer->getParam( 'nonexistent' )
		);
	}

	public function testFilenameFormat() {
		$this->assertEquals(
			'MyExtension',
			Sanitizer::getFilenameFormat( '../../MyExtension' )
		);
		$this->assertEquals(
			'MyExtensionOrSomething',
			Sanitizer::getFilenameFormat( '../../MyExtension/../OrSomething' )
		);
		$this->assertEquals(
			'My_Extension_OrSomething',
			Sanitizer::getFilenameFormat( 'My_Extension_OrSomething' )
		);
		$this->assertEquals(
			'MyExtension',
			Sanitizer::getFilenameFormat( ' My Extension ' )
		);
	}

	public function testSanitizerLowerCamelFormat() {
		$this->assertEquals(
			'myExtension',
			Sanitizer::getLowerCamelFormat( 'MyExtension' )
		);
		$this->assertEquals(
			'myExtension',
			Sanitizer::getLowerCamelFormat( 'My Extension' )
		);
		$this->assertEquals(
			'myExtension',
			Sanitizer::getLowerCamelFormat( 'My.Extension' )
		);
		$this->assertEquals(
			'someExtensionSomething',
			Sanitizer::getLowerCamelFormat( 'Some-extension something' )
		);
		$this->assertEquals(
			'someExtensionSomeThing',
			Sanitizer::getLowerCamelFormat( 'Some-extension someThing' )
		);
	}

	public function testSanitizerValidate() {
		$tests = [
			'names' => [
				'valid' => [ 'MyExt', 'MyExt123', 'MyExt_12-3' ],
				'invalid' => [ 'My ext', '\\n', '', '123456789012345678901234567890123' ]
			],
			'numbers' => [
				'valid' => [ '132', '0', '123' ],
				'invalid' => [ 'str', '123s' ],
			],
			'booleans' => [
				'valid' => [ 'true', true, '1' ],
			],
			'urls' => [
				'valid' => [
					'http://example.com',
					'http://mediawiki.org/wiki/Extension:Something',
				],
				'invalid' => [
					'http://',
					'something.com'
				],
			],
		];

		foreach ( $tests as $type => $strings ) {
			if ( isset( $strings[ 'valid' ] ) ) {
				foreach ( $strings[ 'valid' ] as $str ) {
					$this->assertTrue(
						Sanitizer::validate( $type, $str )
					);
				}
			}

			if ( isset( $strings[ 'invalid' ] ) ) {
				foreach ( $strings[ 'invalid' ] as $str ) {
					$this->assertNotTrue(
						Sanitizer::validate( $type, $str )
					);
				}
			}
		}
	}
}

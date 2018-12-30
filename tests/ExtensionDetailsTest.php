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
			'specialpage_intro' => 'A great new special foobar page.'
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
	}

}

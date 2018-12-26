<?php

use PHPUnit\Framework\TestCase;
class BuilderTest extends TestCase {

	public function testBuilderInstance() {
		$params = array(
			'text' => 'response',
			'array' => array( 1, 2, 3 ),
			'object' => array( 'one' => 'two' ),
		);

		$builder = new MWStew\Builder\Builder( '', 'My Extension' );

		$this->assertInstanceOf(MWStew\Builder\Builder::class, $builder);
	}
}

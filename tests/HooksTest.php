<?php

use MWStew\Builder\Hooks;
use PHPUnit\Framework\TestCase;

class HooksTest extends TestCase {
	public function testInstance() {
		$hooks = new Hooks();

		$this->assertEquals(
			// This number might need to be updated
			// when hooks are pulled from the API again
			608,
			count( $hooks->getHookNames() )
		);
	}
	public function testExtractParamDocumentation() {
		$cases = [
			[
				'input' => 'WikiPage &$article, User &$user, &$reason, &$error',
				'expected' => [
					'WikiPage &$article',
					'User &$user',
					'&$reason',
					'&$error'
				],
			],
			[
				'input' => '',
				'expected' => null,
			],
		];

		foreach ( $cases as $testCase ) {
			$this->assertEquals(
				$testCase[ 'expected' ],
				Hooks::extractParamDocArray( $testCase[ 'input' ] )
			);
		}
	}

	public function testCreateFunctionNameFromHookName() {
		$cases = [
			'foo' => 'onFoo',
			'foo::bar' => 'onFooBar',
			'EditPage::importFormData' => 'onEditPageImportFormData',
			'EditPage::showEditForm:initial' => 'onEditPageShowEditFormInitial',
			'EditPage::showEditForm:fields' => 'onEditPageShowEditFormFields'
		];

		foreach ( $cases as $hookName => $expected ) {
			$this->assertEquals(
				$expected,
				Hooks::createFunctionNameFromHookName( $hookName )
			);
		}
	}

	public function testGetHookContent() {
		$cases = [
			'ArticleDelete' => 'hooks.ArticleDelete.php',
			'AddNewAccount' => 'hooks.AddNewAccount.php',
			'EditPage::importFormData' => 'hooks.EditPageImportFormData.php',
			'EditPage::showReadOnlyForm:initial' => 'hooks.EditPageShowReadOnlyFormInitial.php',
			'fooBarBaz' => 'hooks.unrecognized.php'
		];

		$hooks = new Hooks();
		foreach ( $cases as $hookName => $hookFile ) {
			$expected = file_get_contents( __DIR__ . '/data/' . $hookFile );
			$this->assertEquals(
				$expected,
				$hooks->getHookContent( $hookName ),
				'Hook content for "' . $hookName . '"'
			);
		}
	}
	public function testNormalizeHookName() {
		$cases = [
			'articledelete' => 'ArticleDelete',
			'adDNeWaCcOuNt' => 'AddNewAccount',
			'editpage::importformdata' => 'EditPage::importFormData',
			'fooBarBaz' => 'FooBarBaz', // Not found; capitalize
			'f%oo B*&a%rB!a|z123  ' => 'FooBarBaz123', // Not found; clean up and capitalize
		];

		$hooks = new Hooks();
		foreach ( $cases as $lookup => $expected ) {
			$this->assertEquals(
				$expected,
				$hooks->normalizeHookName( $lookup ),
				"Normalizing: $lookup => $expected"
			);
		}

	}
}

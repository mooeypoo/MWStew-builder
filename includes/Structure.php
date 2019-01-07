<?php

namespace MWStew\Builder;

class Structure {
	/**
	 * Create a file map based on the given template
	 * parameters. This will translate the variables
	 * that are used in the file names to the variables
	 * that match the ones in the parameters
	 *
	 * @see ExtensionDetails#getTemplateParams for the
	 *  expected keys and values for the parameter.
	 * @param Array $params Template-ready parameters.
	 *  Expects the structure of parameters that are
	 *  outputted for twig templates.
	 * @return Array An object representing the output
	 *  filenames and the input template files they are
	 *  based on.
	 */
	public static function getFileMap( $params = [] ) {
		$output = [];
		$varmap = [
			'%NAME%' => [ 'name' ],
			'%LOWER_CAMEL_NAME%' => [ 'lowerCamelName' ],
			'%SPECIAL_CLASSNAME%' => [ 'specialpage', 'className' ],
			'%LICENSE%' => [ 'license' ]
		];

		$structure = self::getFileStructure();
		$fileList = $structure[ 'always' ];
		if ( $params[ 'license' ] ) {
			$fileList = $fileList + $structure[ 'license' ];
		}
		if ( $params[ 'parts' ][ 'javascript' ] ) {
			$fileList = $fileList + $structure[ 'js' ];
		}
		if ( $params[ 'parts' ][ 'php' ] ) {
			$fileList = $fileList + $structure[ 'php' ];
		}
		if ( $params[ 'specialpage' ][ 'exists' ] ) {
			$fileList = $fileList + $structure[ 'special' ];
		}
		if ( count( $params[ 'hooksReference' ] ) > 0 ) {
			$fileList = $fileList + $structure[ 'hooks' ];
		}

		foreach ( $fileList as $outputFile => $templateName ) {
			// See if the filename needs to be changed
			$newOutputFilename = $outputFile;
			$newTemplateName = $templateName;
			foreach ( array_keys( $varmap ) as $replaceVar ) {
				// Replace in output file
				$replaceWith = Generator::getObjectProp( $params, $varmap[ $replaceVar ] );
				if ( $replaceWith !== null ) {
					$newOutputFilename = str_replace( $replaceVar, $replaceWith, $newOutputFilename );
				}

				// Replace in template name
				$replaceWith = Generator::getObjectProp( $params, $varmap[ $replaceVar ] );
				if ( $replaceWith !== null ) {
					$newTemplateName = str_replace( $replaceVar, $replaceWith, $newTemplateName );
				}
			}
			$template = !$templateName ?
				$newOutputFilename : $newTemplateName;

			// Build the output
			$output[ $newOutputFilename ] = $template;
		}

		return $output;
	}

	public static function getFileStructure() {
		return [
			'always' => [
				'extension.json' => '',
				'CODE_OF_CONDUCT.md' => '',
				'i18n/en.json' => '',
				'i18n/qqq.json' => '',
			],
			'license' => [
				'COPYING' => '%LICENSE%.txt'
			],
			'js' => [
				'.eslintrc.json' => '',
				'.stylelintrc' => '',
				'Gruntfile.js' => '',
				'package.json' => '',
				'modules/ext.%LOWER_CAMEL_NAME%.js' => 'modules/ext.extension.js',
				'modules/ext.%LOWER_CAMEL_NAME%.css' => 'modules/ext.extension.css',
				'tests/%NAME%.test.js' => 'tests/qunit.js',
				'tests/.eslintrc.json' => ''
			],
			'php' => [
				'composer.json' => '',
				'tests/%NAME%.test.php' => 'tests/phpunit.php'
			],
			'hooks' => [
				'%NAME%Hooks.php' => 'Hooks.php',
			],
			'special' => [
				'specials/%SPECIAL_CLASSNAME%.php' => 'specials/SpecialPage.php',
				'%NAME%.alias.php' => 'extension.alias.php'
			]
		];
	}
}

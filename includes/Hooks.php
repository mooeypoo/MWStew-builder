<?php

namespace MWStew\Builder;

/**
 * Read hook data and parse it for processing twig templates
 */
class Hooks {
	protected $data;
	protected $templating;
	protected $lookupMap = [];

	public function __construct( $templating = null ) {
		$filename = dirname( __DIR__ ) . '/templates/_hooks/data/hooks.json';
		$this->data = json_decode( file_get_contents( $filename ), true );
		$this->templating = $templating;
		if ( !$this->templating ) {
			$this->templating = new Templating();
		}

		// Create a search map (lowercase => mixed case) for searches
		foreach ( array_keys( $this->data ) as $hookName ) {
			$this->lookupMap[ strtolower( $hookName ) ] = $hookName;
		}
	}

	public function getHookNames() {
		return array_keys( $this->data );
	}

	/**
	 * Get an array with the contents of the requested hooks
	 *
	 * @param array $hookNames Array of hook names
	 * @return array Hook method contents for the Hooks.php file
	 */
	public function getHooksMethods( $hookNames = [] ) {
		$content = [];
		foreach ( $hookNames as $hook ) {
			$content[] = $this->getHookContent( $hook );
		}

		return $content;
	}

	public function getHookContent( $hookName ) {
		$hookName = $this->normalizeHookName( $hookName );
		$data = $this->getHookData( $hookName );
		if ( !$data ) {
			$data = [
				'name' => $hookName,
				'summary' => 'A method to respond to hook ' .  $hookName,
				'unrecognized' => true
			];
		}
		$argsarr = isset( $data[ 'args' ] ) ? $this->extractParamDocArray( $data[ 'args' ] ): null;
		$params = array_merge(
			$data,
			[
				'functionName' => self::createFunctionNameFromHookName( $hookName ),
				'argsarr' => $argsarr,
			]
		);

		return $this->templating->render( '_hooks/_BaseHookPartial.php', $params );
	}

	public static function extractParamDocArray( $args = '' ) {
		if ( !$args ) {
			return null;
		}

		$pieces = explode( ',', $args );
		return array_map(
			function ( $arg ) { return trim( $arg ); },
			$pieces
		);
	}

	/**
	 * Create the array for the definition needed for extension.json
	 * hooks array.
	 * The response is in the form of
	 * [
	 *   [ HookOne => extNameHooks::onHookOne ],
	 *   [ HookTwo => extNameHooks::onHookTwo ]
	 * ]
	 *
	 * @param  string $extName Extension name
	 * @param  array $hooks An array of strings representing hooks
	 * @return array Array consisting of array to method needed by extension.json
	 */
	public static function getExtJsonHookDefinitionArray( $extName, $hooks = [] ) {
		$hookDefinition = [];
		foreach ( $hooks as $hookName ) {
			$hookDefinition[ $hookName ] = "{$extName}Hooks::" . static::createFunctionNameFromHookName( $hookName );
		}

		return $hookDefinition;
	}

	public static function createFunctionNameFromHookName( $hookName ) {
		$className = $hookName;
		// Remove :: and capitalize after
		// Remove : and capitalize after
		foreach ( [ '::', ':' ] as $sep ) {
			$pieces = explode( $sep, $hookName );
			foreach ( $pieces as &$piece ) {
				$piece = ucwords( $piece );
			}

			$className = implode( '', $pieces );
		}

		return 'on' . $className;
	}

	public function getHookData( $hookName = '' ) {
		$hookName = $this->normalizeHookName( $hookName );

		$data = null;
		if ( $hookName !== null ) {
			$data = Generator::getObjectProp( $this->data, [ $hookName ] );
		}

		return $data;
	}

	/**
	 * Normalize the given hook name:
	 * - If the name is found in the system, normalize it
	 * to use the proper capitalization.
	 * - If it is not found, return it as-is
	 *
	 * @param string $hookName Given hook name
	 * @return string Normalized hook name
	 */
	public function normalizeHookName( $hookName = '' ) {
		$foundHookName = Generator::getObjectProp( $this->lookupMap, [ strtolower( $hookName ) ] );

		if ( !$foundHookName ) {
			$hookName = ucfirst( preg_replace( '/[^A-Za-z0-9\-]/', '', $hookName ) );
		}
		// Unrecognized hooks should at least be capitalized
		return $foundHookName ? $foundHookName : ucfirst( $hookName );
	}
}

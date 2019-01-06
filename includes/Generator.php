<?php

namespace MWStew\Builder;

class Generator {

	/**
	 * Generate the needed extension files based on given
	 * parameter values.
	 *
	 * @param array $data An object representing the extension
	 *  data required to create the extension files. See expected
	 *  keys:
	 *  $data = [
	 *    'name' => (string) Extension name; English only, no spaces
	 *    'title' => (string) Extension title or display name
	 *    'author' => (string) Extension author
	 *    'version' => (string|number) Extension version
	 *    'description' => (string) A short description for the
	 *      extension.
	 *    'url' => (string) A URL for the extension
	 *    'license' => (string) License code for the extension.
	 *      Expected a valid value to be used in composer.json and
	 *      package.json
	 *    'dev_php' => (bool) Whether the extension should have
	 *      the base files needed for a PHP development environment.
	 *    'dev_js' => (bool) Whether the extension should have
	 *      the base files needed for a JavaScript development environment.
	 *    'specialpage_name' => (string) A name for a sepcial page.
	 *      Must use valid characters for MediaWiki title.
	 *    'specialpage_title' => (string) A title for the special page.
	 *    'specialpage_intro' => (string) A short description or introduction
	 *      text for the special page. This will appear at the top of the
	 *      new special page that is created.
	 *  ]
	 * @param array $config Configuration object.
	 *  $config = [
	 *    'prefix' => (string) Optional prefix for field names. If given,
	 *      all data keys will be expected to have the same given prefix.
	 *      This can be used to differentiate different forms for producing
	 *      extensions or skins in the same request.
	 *    'cacheDir' => (string) A directory for the extension file cache.
	 *      if not given, an internal cache is used.
	 *  ]
	 */
	public function __construct( $data = [], $config = [] ) {
		$this->prefix = $this->getObjectProp( $config, [ 'prefix' ] );
		$this->cacheDir = $this->getObjectProp( $config, [ 'cacheDir' ] );

		$this->sanitizer = new Sanitizer( $data, $this->prefix );
		$this->details = new ExtensionDetails( $this->sanitizer->getRawParams(), $this->prefix );

		$this->templating = new Templating( $this->cacheDir );
		$this->packager = new Packager();

		$params = $this->details->getAllParams();

		// Build the file package
		$structure = Structure::getFileMap( $params );
		foreach ( $structure as $filename => $templateName ) {
			$this->packager->addFile( $filename, $this->templating->render( $templateName, $params ) );
		}
	}

	public function getFiles() {
		return $this->packager->getFiles();
	}

	/**
	 * Get a property from an object. If the property isn't found,
	 * returning null.
	 *
	 * @param  Array $obj Requested object
	 * @param  array  $prop An array of properties to traverse into
	 *  to find the requested value.
	 * @return Mixed value
	 */
	public static function getObjectProp( $obj, $prop = [] ) {
		$reference = $obj;
		foreach ( $prop as $p ) {
			if ( !isset( $reference[ $p] ) ) {
				return null;
			}

			$reference = $reference[ $p ];
		}

		return $reference;
	}
}

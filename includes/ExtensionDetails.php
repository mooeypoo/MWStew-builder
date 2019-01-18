<?php

namespace MWStew\Builder;

class ExtensionDetails {
	protected $param_prefix = '';

	protected $name = '';
	protected $title = '';
	protected $author = '';
	protected $version = '0.0.0';
	protected $desc = '';
	protected $url = '';
	protected $license = '';

	/**
	 * Licenses we know about
	 */
	protected $knownLicenses = [ 'GPL-2.0+', 'MIT', 'Apache-2.0' ];

	protected $devEnvironment = array();

	protected $specialName = '';
	protected $specialTitle = '';
	protected $specialIntro = '';

	protected $hooks = array();

	public function __construct( $formParams, $prefix = '' ) {
		$this->param_prefix = $prefix;
		$this->rawParams = $formParams;
		$this->hooksHelper = new Hooks();

		$this->setName( $this->getRawParam( 'name' ) );
		$this->setTitle( $this->getRawParam( 'title' ) );
		$this->setAuthor( $this->getRawParam( 'author' ) );
		$this->setVersion( $this->getRawParam( 'version' ) );
		$this->setDescription( $this->getRawParam( 'description' ) );
		$this->setURL( $this->getRawParam( 'url' ) );
		$this->setLicense( $this->getRawParam( 'license' ) );

		$this->setDevEnv(
			$this->getRawParam( 'dev_php' ),
			$this->getRawParam( 'dev_js' )
		);

		$this->setSpecialPage(
			$this->getRawParam( 'specialpage_name' ),
			$this->getRawParam( 'specialpage_title' ),
			$this->getRawParam( 'specialpage_intro' )
		);

		$this->setHooks( $this->getRawParam( 'hooks', [] ) );
	}

	protected function getRawParam( $paramName, $default = null ) {
		return isset( $this->rawParams[ $this->param_prefix . $paramName ] ) ?
			$this->rawParams[ $this->param_prefix . $paramName ] :
			$default;
	}

	public function setName( $name ) {
		$this->name = str_replace( ' ', '', Sanitizer::getFilenameFormat( $name ) );
	}

	public function setTitle( $title ) {
		$this->title = $title ? $title : $this->name;
	}

	public function setAuthor( $author ) {
		$this->author = $author ? $author : '';
	}

	public function setVersion( $version ) {
		$this->version = $version ? $version : '0.0.0';
	}

	public function setDescription( $desc ) {
		$this->desc = $desc ? $desc : '';
	}

	public function setURL( $url ) {
		$this->url = $url ? $url : '';
	}

	public function setLicense( $license ) {
		if ( in_array( $license, $this->knownLicenses ) ) {
			$this->license = $license;
		}
	}

	public function setDevEnv( $isPhp, $isJs ) {
		$this->devEnvironment[ 'php' ] = (bool)$isPhp;
		$this->devEnvironment[ 'js' ] = (bool)$isJs;
	}

	public function setSpecialPage( $name, $title = '', $intro = '' ) {
		$this->specialName = $name ? $name : '';
		$this->specialTitle = $title ? $title : '';
		$this->specialIntro = $intro ? $intro : '';
	}

	public function setHooks( $hooks ) {
		$this->hooks = $hooks ? $hooks : array();
	}

	public function getHooks() {
		return $this->hooks;
	}

	public function getName() {
		return $this->name;
	}

	public function getLowerCamelName() {
		return Sanitizer::getLowerCamelFormat( $this->name );
	}

	public function getClassName() {
		return $this->name;
	}

	public function isEnvironment( $env ) {
		return (
			isset( $this->devEnvironment[ $env ] ) ?
				$this->devEnvironment[ $env ] : false
		);
	}

	public function getLicense() {
		return $this->license;
	}

	public function hasSpecialPage() {
		return ( (bool) $this->specialName );
	}

	public function getSpecialPageClassName() {
		return 'Special' . str_replace( ' ', '_', $this->specialName );
	}

	public function getTemplateParams() {
		$params = array(
			'name' => $this->name,
			'lowerCamelName' => $this->getLowerCamelName(),
			'title' => $this->title,
			'author' => $this->author,
			'version' => $this->version,
			'license' => $this->license,
			'desc' => $this->desc,
			'url' => $this->url,
			'parts' => array(
				'javascript' => $this->isEnvironment( 'js' ),
				'php' => $this->isEnvironment( 'php' ),
			),
			'specialpage' => array(
				'exists' => $this->hasSpecialPage(),
				'name' => array(
					'name' => $this->specialName,
					'lowerCamelName' => Sanitizer::getLowerCamelFormat( $this->specialName ),
					'i18n' => $this->hasSpecialPage() ? $this->getSpecialPageKeyFormat() : '',
				),
				'className' => $this->hasSpecialPage() ? $this->getSpecialPageClassName() : '',
				'title' => $this->specialTitle,
				'intro' => $this->specialIntro,
			),
			'hooksReference' => [],
			'hookMethods' => [],
		);

		// Create the Hooks extension.json data
		$hookDefinition = Hooks::getExtJsonHookDefinitionArray( $this->name, $this->getHooks() );
		if ( $this->isEnvironment( 'js' ) ) {
			// Add the Javascript test hook if needed
			$hookDefinition[ 'ResourceLoaderTestModules' ] = "{$this->name}Hooks::onResourceLoaderTestModules";
		}

		// Transform to a flat array:
		$hookArray = [];
		foreach ( $hookDefinition as $hookName => $localReference ) {
			$hookArray[] = '"' . $hookName . '": [ "' . $localReference . '" ]';
			// $hookArray[] = [
			// 	'name' => $hookName,
			// 	'reference' => $localReference,
			// ];
		}
		$params[ 'hooksReference' ] = $hookArray;

		// Collect hooks content
		$params[ 'hookMethods' ] = $this->hooksHelper->getHooksMethods( $this->getHooks() );

		return $params;
	}

	public function getSpecialPageKeyFormat() {
		return 'special-' . Sanitizer::getLowerCamelFormat( $this->specialName );
	}
}

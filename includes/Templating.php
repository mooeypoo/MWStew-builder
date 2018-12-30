<?php

namespace MWStew\Builder;

class Templating {
	protected $loader = null;
	protected $twig = null;
	protected $cacheDir = null;

	public function __construct( $cacheDir = '' ) {
		$this->cacheDir = $cacheDir;
		if ( strlen( $cacheDir ) === 0 ) {
			$this->cacheDir = dirname( __DIR__ ) . '/cache';
		}

		$this->loader = new \Twig_Loader_Filesystem( dirname( __DIR__ ) . '/templates' );
		$this->twig = new \Twig_Environment( $this->loader, array(
			'cache' => $this->cacheDir,
		) );

		// In the new TWig, there's no clear way to clear cache
		// so we need to do it ourselves, or leave cache as-is.
		// Template files themselves are rarely changed, though
		// TODO: Find a safe way to clear the cache folder,
		// especially since it is configurable.
		// $this->clearCache();
	}

	public function render( $templateName, $data = array() ) {
		$filename = $templateName . '.twig';

		return $this->twig->render( $filename, $data );
	}
}

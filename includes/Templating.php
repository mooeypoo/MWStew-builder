<?php

namespace MWStew\Builder;

class Templating {
	protected $loader = null;
	protected $twig = null;
	protected $cacheDir = null;

	public function __construct( $cacheDir = '' ) {
		$this->cacheDir = $cacheDir;
		if ( strlen( $cacheDir ) === 0 ) {
			$this->cacheDir = 'cache';
		}

		$this->loader = new \Twig_Loader_Filesystem( dirname( __DIR__ ) . '/templates' );
		$this->twig = new \Twig_Environment( $this->loader, array(
			'cache' => dirname( __DIR__ ) . '/' . $this->cacheDir,
		) );

		$this->clearCache();
	}

	public function render( $templateName, $data = array() ) {
		$filename = $templateName . '.twig';

		return $this->twig->render( $filename, $data );
	}

	public function clearCache() {
		// Twig no longer clears cache on its own
		// We'll have to clear it manually
		$files = glob( dirname( __DIR__ ) . '/' . $this->cacheDir . '/*' );
		foreach( $files as $file ) {
			if ( is_file( $file ) ) {
				// unlink( $file );
			}
		}
	}
}

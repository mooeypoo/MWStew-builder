<?php

namespace MWStew\Builder;

class Templating {
	protected $loader = null;
	protected $twig = null;

	public function __construct( $cacheDir = '' ) {
		if ( strlen( $cacheDir ) === 0 ) {
			$cacheDir = dirname( __DIR__ ) . '/cache';
		}

		$this->loader = new \Twig_Loader_Filesystem( dirname( __DIR__ ) . '/templates' );
		$this->twig = new \Twig_Environment( $this->loader, array(
			'cache' => $cacheDir,
		) );
		$this->twig->clearCacheFiles();
	}

	public function render( $templateName, $data = array() ) {
		$filename = $templateName . '.twig';

		return $this->twig->render( $filename, $data );
	}
}

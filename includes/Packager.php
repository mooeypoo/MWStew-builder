<?php

namespace MWStew\Builder;

/**
 * Class to package the hierarchical file information
 * for the extension.
 */
class Packager {
	protected $files = array();

	public function addFile( $filename, $content ) {
		$this->files[ $filename ] = $content;
	}

	public function getFiles() {
		return $this->files;
	}
}

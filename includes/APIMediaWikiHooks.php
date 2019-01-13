<?php

namespace MWStew\Builder;

/**
 * This is a class that pulls hooks from MediaWiki
 * It should only be used on for builds
 */
class APIMediaWikiHooks {
	public static $MAX_REQUESTS = 50;

	public function process( $hooks = null ) {
		$templateRegex = '/(\{\{( ?\{\{)?(TNTN?\|)?MediaWikiHook(\}\})?)(.*?)\}\}/';
		$processed = [];
		$unprocessed = [];

		if ( !$hooks ) {
			$hooks = $this->getAllHooksFromAPI();
		}

		foreach ( $hooks as $name => $wikitext ) {
			// echo "Processing $name\n";
			// Extract the {{TNT|MediaWikiHook ... }} template
			$wikitext = trim(preg_replace('/\s+/', ' ', $wikitext));
			preg_match( $templateRegex, $wikitext, $matches );

			if ( count( $matches ) === 0 ) {
				$unprocessed[ $name ] = $wikitext;
				continue;
			}

			$templWikitextParams = trim( $matches[ count( $matches ) - 1 ] );
			$templWikitextParamsArray = explode( '|', $templWikitextParams );
			$processed[ $name ] = [];
			foreach ( $templWikitextParamsArray as $twParam ) {
				if ( $twParam ) {
					$data = explode( '=', $twParam );
					if ( count( $data ) === 2 ) {
						$processed[ $name ][ trim( $data[0] ) ] = trim( $data[1] );
					}
				}
			}
		}
		return [
			'processed' => $processed,
			'unprocessed' => $unprocessed
		];
	}

	public function getAllHooksFromAPI() {
		$offset = 0;
		$count = 1;
		$hooks = [];

		echo "Fetching data from the API.\n";
		do {
			$response = $this->getFromAPI( $offset );
			echo "Request sent (offset: $offset, count: $count [max: " . self::$MAX_REQUESTS . "])\n";
			$offset = Generator::getObjectProp( $response, [ 'continue', 'gpsoffset' ] );

			$this->extractPagesFromResponse( $hooks, $response );

			$count++;
			usleep( 500 ); // Half a second delay between requests
		} while ( $offset !== null && $count < self::$MAX_REQUESTS );

		return $hooks;
	}

	protected function extractPagesFromResponse( &$arr, $response ) {
		$pagesDump = Generator::getObjectProp( $response, [ 'query', 'pages' ] );

		if ( !$pagesDump ) {
			echo "No data. Skipping.\n";
			// Sanity check... this should never happen
			return [];
		}

		foreach ( $pagesDump as $pageID => $pageData ) {
			$fullTitle = Generator::getObjectProp( $pageData, [ 'title' ] );
			if ( !$fullTitle ) {
				continue;
			}

			$pageName = preg_replace( "/^Manual:Hooks(\/?)/", '', $fullTitle );
			$pageContent = Generator::getObjectProp( $pageData, [ 'revisions', 0, 'slots', 'main', '*' ] );

			if ( $pageName && $pageContent ) {
				$arr[ $pageName ] = $pageContent;
			} else {
				echo "!! SKIPPING $pageName: $pageContent\n";
			}
		}

		return $arr;
	}

	protected function getFromAPI( $offset = 0 ) {
		$curl = curl_init();

		$data = [
			'format' => 'json',
			'action' => 'query',
			'prop' => 'revisions',
			'rvslots' => 'main',
			'rvprop' => 'content',
			'rvsection' => 0, // 0 - intro; 1 - details
			'generator' => 'prefixsearch',
			'gpssearch' => 'Hooks/',
			'gpsnamespace' => 100, // "Manual:"
			'gpslimit' => 50,
			'gpsoffset' => $offset
		];
		$url = 'https://www.mediawiki.org/w/api.php?' . http_build_query( $data );

		curl_setopt_array( $curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_TIMEOUT => 30,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => "GET",
		  CURLOPT_HTTPHEADER => array(
			"cache-control: no-cache"
		  ),
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);
		curl_close($curl);

		return json_decode( $response, true );
	}
}

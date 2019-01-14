<?php

use MWStew\Builder\APIMediaWikiHooks;
use PHPUnit\Framework\TestCase;

class APIMediaWikiHooksTest extends TestCase {
	public function testProcess() {
		$cases = [
			[
				'input' => [
					// Expected 'normal' template definition
					"ArticleDeleteComplete" => '{{TNT|MediaWikiHook\n|name=ArticleDeleteComplete\n|version=1.4.0\n|args=&$article, User &$user, $reason, $id, $content, LogEntry $logEntry, $archivedRevisionCount\n|source=Article.php\n|summary=Occurs after the delete article request has been processed\n}}',
					"ArticleFromTitle" => '{{TNT|MediaWikiHook\n|name=ArticleFromTitle\n|version=1.8.0\n|args=Title &$title, &$article, $context\n|source=Article.php\n|summary=Called to determine the class to handle the article rendering, based on title\n}}',

					// TNTN|MediaWikiHook templatem nested in another... template?
					"LoadExtensionSchemaUpdates" => '{{ {{TNTN|MediaWikiHook}}\n|name = LoadExtensionSchemaUpdates\n|summary = Fired when MediaWiki is updated to allow extensions to update the database\n|version = 1.10.1\n|args = DatabaseUpdater $updater\n|source = DatabaseUpdater.php\n|sourceclass = DatabaseUpdater\n|sourcefunction = __construct\n}}',

					// Spaces in the param definition
					"PrefixSearchExtractNamespace" => '{{TNT|MediaWikiHook\n|name = PrefixSearchExtractNamespace\n|version = 1.25.0\n|gerrit = 168167\n|removed =\n|summary = Called if core was not able to extract a namespace from the search string so that extensions can attempt it.\n|args = &$namespaces, &$search\n|source = PrefixSearch.php\n|source2 = SearchEngine.php\n|sourcefunction =\n|sourceclass =\n|newvarname =\n|newvarlink =\n}}',

					// TNT|MediaWikiHook + translation
					"MovePageCheckPermissions/en" => '<languages \/>\n{{TNT|MediaWikiHook\n|name=MovePageCheckPermissions\n|version=1.25.0\n|args=Title $oldTitle, Title $newTitle, User $user, $reason, Status $status\n|source=MovePage.php\n|summary=Specify whether the user is allowed to move the page.\n}}',
					// MediaWikiHook template
					"ParserFirstCallInit/en" => '<languages\/>\n{{MediaWikiHook\n|name=ParserFirstCallInit\n|version=1.12.0\n|args=Parser &$parser\n|summary=called when the parser initializes for the first time\n|source=Parser.php\n|sourceclass=Parser\n|sourcefunction=firstCallInit\n}}',

					// Contains ::
					"Article::MissingArticleConditions" => '{{TNT|MediaWikiHook\n|name=Article::MissingArticleConditions\n|version=1.23.0\n|args=&$conds, $logTypes\n|source=Article.php\n|summary=Before fetching deletion & move log entries to display a message of a non-existing page being deleted\/moved, give extensions a chance to hide their (unrelated) log entries.\n}}',

					// English translation + has a space (replaced with underscore)
					"RecentChange save/en" => '<languages \/>\n{{TNT|MediaWikiHook\n|name=RecentChange_save\n|version=1.8.0\n|args=&$recentChange\n|source=RecentChange.php\n|summary=called at the end of RecentChange::save()\n}}',

					// Non English translation (=> unprocessed)
					"FooBarBaz/fr" => '{{MediaWikiHook\n|name=ParserFirstCallInit\n|version=1.12.0\n|args=Parser &$parser\n|summary=called when the parser initializes for the first time\n|source=Parser.php\n|sourceclass=Parser\n|sourcefunction=firstCallInit\n}}',

					// Undocumented (=> unprocessed)
					"ApiMain::moduleManager" => "{{Undocumented Mediawiki Hook}}",
				],
				'expected' => [
					'unprocessed' => [
						"ApiMain::moduleManager" => "{{Undocumented Mediawiki Hook}}",
						"FooBarBaz/fr" => '{{MediaWikiHook\n|name=ParserFirstCallInit\n|version=1.12.0\n|args=Parser &$parser\n|summary=called when the parser initializes for the first time\n|source=Parser.php\n|sourceclass=Parser\n|sourcefunction=firstCallInit\n}}',
					],
					'processed' => [
						'ArticleDeleteComplete' => [
							'name' => 'ArticleDeleteComplete',
							'version' => '1.4.0',
							'args' => '&$article, User &$user, $reason, $id, $content, LogEntry $logEntry, $archivedRevisionCount',
							'summary' => 'Occurs after the delete article request has been processed',
							'source' => 'Article.php'
						],
						'ArticleFromTitle' => [
							'name' => 'ArticleFromTitle',
							'version' => '1.8.0',
							'args' => 'Title &$title, &$article, $context',
							'summary' => 'Called to determine the class to handle the article rendering, based on title',
							'source' => 'Article.php'
						],
						'LoadExtensionSchemaUpdates' => [
							'name' => 'LoadExtensionSchemaUpdates',
							'version' => '1.10.1',
							'args' => 'DatabaseUpdater $updater',
							'summary' => 'Fired when MediaWiki is updated to allow extensions to update the database',
							'source' => 'DatabaseUpdater.php'
						],
						'PrefixSearchExtractNamespace' => [
							'name' => 'PrefixSearchExtractNamespace',
							'version' => '1.25.0',
							'args' => '&$namespaces, &$search',
							'summary' => 'Called if core was not able to extract a namespace from the search string so that extensions can attempt it.',
							'source' => 'PrefixSearch.php'
						],
						'MovePageCheckPermissions' => [
							'name' => 'MovePageCheckPermissions',
							'version' => '1.25.0',
							'args' => 'Title $oldTitle, Title $newTitle, User $user, $reason, Status $status',
							'summary' => 'Specify whether the user is allowed to move the page.',
							'source' => 'MovePage.php'
						],
						'ParserFirstCallInit' => [
							'name' => 'ParserFirstCallInit',
							'version' => '1.12.0',
							'args' => 'Parser &$parser',
							'summary' => 'called when the parser initializes for the first time',
							'source' => 'Parser.php'
						],
						'Article::MissingArticleConditions' => [
							'name' => 'Article::MissingArticleConditions',
							'version' => '1.23.0',
							'args' => '&$conds, $logTypes',
							'summary' => 'Before fetching deletion & move log entries to display a message of a non-existing page being deleted\/moved, give extensions a chance to hide their (unrelated) log entries.',
							'source' => 'Article.php'
						],
						'RecentChange_save' => [
							'name' => 'RecentChange_save',
							'version' => '1.8.0',
							'args' => '&$recentChange',
							'summary' => 'called at the end of RecentChange::save()',
							'source' => 'RecentChange.php'
						],
					]
				]
			]
		];

		foreach ( $cases as $testCase ) {
			$api = new APIMediaWikiHooks();
			$this->assertEquals(
				$testCase[ 'expected' ],
				$api->process( $testCase[ 'input' ] )
			);
		}
	}

}

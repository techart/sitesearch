<?php

namespace Techart\SiteSearch\Engine\Mysql\QueryBuilder;

use Techart\SiteSearch\Engine\Mysql\QueryBuilder;
use Techart\SiteSearch\Text\Stemmer;
use Techart\SiteSearch\Text\TextExtractor;

/**
 * Class BooleanMode
 *
 * Реализует full-text поиск в MySQL в режиме BOOLEAN с заменой слов на стеммы.
 *
 * @package Techart\SiteSearch\Engine\Mysql\QueryBuilder
 */

class BooleanMode extends QueryBuilder
{
	protected $stemmer;
	protected $textInQuotesRegexp = '{"[^"]+"}';
	protected $wordMasksRegexp = '{(\*|^-)}';

	protected function queryMode()
	{
		return 'IN BOOLEAN MODE';
	}

	protected function prepareQuery($query)
	{
		$extractor = app(TextExtractor::class, ['str' => $query]);
		$this->extractTextInQuotes($extractor);
		$query = $this->replaceWordsToStemms($extractor, [
			$extractor->extractedTextLabelRegexp(),
			$this->wordMasksRegexp
		], [$this, 'addWildcardToStemm']);
		$extractor->setStr($query);
		return $this->returnTextInQuotes($extractor);
	}

	protected function extractTextInQuotes($extractor)
	{
		return $extractor->extract($this->textInQuotesRegexp);
	}

	protected function returnTextInQuotes($extractor)
	{
		return (string)$extractor->returnTexts();
	}

	protected function replaceWordsToStemms($extractor, $exceptions = null, $callback = null)
	{
		return $this->stemmer()->process($extractor->str(), $exceptions, $callback);
	}

	protected function stemmer()
	{
		if (is_null($this->stemmer)) {
			$this->stemmer = app(Stemmer::class);
		}
		return $this->stemmer;
	}

	public function addWildcardToStemm($word, $stem)
	{
		return "+$stem*";
	}
}
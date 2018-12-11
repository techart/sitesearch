<?php

namespace Techart\SiteSearch\Engine\Mysql\QueryBuilder;

use Techart\SiteSearch\Engine\Mysql\QueryBuilder;

/**
 * Class NaturalLanguageMode
 *
 * Реализует full-text поиск в Mysql в режиме NATURAL LANGUAGE MODE. Через настройку query_expansion так же можно
 * переключить режим в WITH QUERY EXPANSION.
 *
 * @package Techart\SiteSearch\Engine\Mysql\QueryBuilder
 */
class NaturalLanguageMode extends QueryBuilder
{
	protected function queryMode()
	{
		 return $this->isQueryExpansionMode() ? 'WITH QUERY EXPANSION' : 'IN NATURAL LANGUAGE MODE';
	}

	protected function isQueryExpansionMode()
	{
		return config('sitesearch.query_expansion', false);
	}
}
<?php

namespace Techart\SiteSearch\Engine\Mysql;

/**
 * Class QueryBuilder
 *
 * Базовый построитель запроса к таблице поиска
 *
 * @package Techart\SiteSearch\Engine\Mysql
 */
abstract class QueryBuilder
{
	/**
	 * @var Model IndexItem
	 */
	protected $indexItemModel;

	public function __construct()
	{
		$this->indexItemModel = app()->make('\Techart\SiteSearch\Engine\Mysql\Model\IndexItem');
	}

	public function searchableFields()
	{
		return $this->indexItemModel->searchableFields()->implode(',');
	}

	/**
	 * @param string $query
	 * @return Builder
	 */
	public function build($query, $variant)
	{
		$whereRaw = "MATCH({$this->searchableFields()}) AGAINST(? {$this->queryMode()})";
		return $this->indexItemModel
			->whereRaw($whereRaw, [$this->prepareQuery($query)])
			->variant($variant)
		;
	}

	protected function prepareQuery($query)
	{
		return $query;
	}

	abstract protected function queryMode();
}
<?php

namespace Techart\SiteSearch\Engine;

use Techart\SiteSearch\Contract\Engine;
use Techart\SiteSearch\Engine\Mysql\Indexer;
use Techart\SiteSearch\Engine\Mysql\Model\IndexItem;
use Techart\SiteSearch\Engine\Mysql\QueryBuilder;
use TAO\Fields\Model;

/**
 * Class Mysql
 *
 * @package Techart\SiteSearch\Engine
 */
class Mysql implements Engine
{
	/**
	 * @var Model
	 */
	protected $indexItemModel;

	/**
	 * @var Indexer
	 */
	protected $indexer;

	/**
	 * @var QueryBuilder
	 */
	protected $queryBuilder;

	public function __construct(IndexItem $model, Indexer $indexer)
	{
		$this->indexItemModel = $model;
		$this->indexer = $indexer;
	}

	public function initialize()
	{
		\TAO::addDatatype('sitesearch_index', IndexItem::class);

		app()->bind(\Techart\SiteSearch\Contract\IndexItem::class, IndexItem::class);
		app()->bind(\Techart\SiteSearch\Contract\Indexer::class, Indexer::class);
	}

	public function search($query, $variant = false)
	{
		return $this->queryBuilder()->build($query, $variant);
	}

	public function indexer()
	{
		return $this->indexer;
	}

	protected function queryBuilder()
	{
		if (is_null($this->queryBuilder)) {
			if (config('sitesearch.mysql.mode', 'boolean')) {
				$class = QueryBuilder\BooleanMode::class;
			} else {
				$class = QueryBuilder\NaturalLanguageMode::class;
			}
			$this->queryBuilder = app($class);
		}
		return $this->queryBuilder;
	}
}
<?php

namespace Techart\SiteSearch\Engine\Mysql;

use Techart\SiteSearch\Engine\Mysql\Model\IndexItem;
use Techart\SiteSearch\Searchable;
use TAO\Fields\Model;
use Techart\SiteSearch\SiteSearch;

class Indexer implements \Techart\SiteSearch\Contract\Indexer
{
	/**
	 * @var Model IndexItem
	 */
	protected $indexItemModel;

	public function __construct(IndexItem $indexItemModel)
	{
		$this->indexItemModel = $indexItemModel;
	}

	/**
	 * @param Model|Searchable $model
	 * @throws \TAO\Fields\Exception\UndefinedField
	 */
	public function update($model)
	{
		if ($this->component()->isSearchableModel($model)) {
			$indexItem = $this->findIndexItemByModel($model);
			if (!$indexItem) {
				$indexItem = $this->createIndexItem();
			}
			$this->assignModelToIndexItem($model, $indexItem);
			$indexItem->save();
		} else {
			$this->delete($model);
		}
	}

	/**
	 * @param Model|Searchable $model
	 * @throws \Exception
	 */
	public function delete($model)
	{
		$indexItem = $this->findIndexItemByModel($model);
		if ($indexItem) {
			$indexItem->delete();
		}
	}

	/**
	 * @param Model $model
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	protected function findIndexItemByModel($model)
	{
		return $this->indexItemModel->byModel($model)->first();
	}

	protected function createIndexItem()
	{
		return $this->indexItemModel->newInstance();
	}

	/**
	 * @param Model|Searchable $model
	 * @param IndexItem $indexItem
	 * @throws \TAO\Fields\Exception\UndefinedField
	 */
	protected function assignModelToIndexItem($model, $indexItem)
	{
		$indexItem->field('datatype_code')->set($model->getDatatype());
		$indexItem->field('model_id')->set($model->getKey());

		$indexItem->field('title')->set($model->searchableTitle());
		$indexItem->field('url')->set($model->searchableUrl());
		$indexItem->field('content')->set($model->searchableContent());
		$indexItem->field('extra')->set($model->searchableExtraData());
	}

	protected function component()
	{
		return app(SiteSearch::class);
	}
}
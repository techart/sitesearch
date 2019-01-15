<?php

namespace Techart\SiteSearch\Engine\Mysql;

use TAO\ORM\Model;
use Techart\SiteSearch\Engine\Mysql\Model\IndexItem;
use Techart\SiteSearch\Searchable;
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
	 * @param Model|Searchable $item
	 * @throws \TAO\Fields\Exception\UndefinedField
	 */
	public function update($item)
	{
		if ($this->component()->isSearchableItem($item)) {
			$indexItem = $this->findIndexItemByModelItem($item);
			if (!$indexItem) {
				$indexItem = $this->createIndexItem();
			}
			$this->assignModelToIndexItem($item, $indexItem);
			$indexItem->save();
		} else {
			$this->delete($item);
		}
	}

	/**
	 * @param Model|Searchable $model
	 * @throws \Exception
	 */
	public function delete($model)
	{
		$indexItem = $this->findIndexItemByModelItem($model);
		if ($indexItem) {
			$indexItem->delete();
		}
	}

	public function clear()
	{
		$this->indexItemModel->truncate();
	}

	/**
	 * @param Model $model
	 * @return \Illuminate\Database\Eloquent\Model|null
	 */
	protected function findIndexItemByModelItem($model)
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
		$indexItem->field('model_key')->set($model->getKey());

		$indexItem->field('title')->set($model->getSearchableTitle());
		$indexItem->field('url')->set($model->getSearchableUrl());
		$indexItem->field('content')->set($model->getSearchableContent());
		$indexItem->field('extra')->set($model->getSearchableExtraData());
	}

	protected function component()
	{
		return app(SiteSearch::class);
	}
}

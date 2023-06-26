<?php

namespace Techart\SiteSearch\Engine\Mysql;

use TAO\ORM\Model;
use Techart\SiteSearch\Engine\Mysql\Model\IndexItem;
use Techart\SiteSearch\Searchable;
use Techart\SiteSearch\SiteSearch;

/**
 * Class Indexer
 *
 * Класс для рабооты с данными для поиска:
 * - индексация доступных для поиска типов данных и записей
 * - исключение недоступных для поиска данных из ранее проиндексированных
 * - удаление всех проиндексированных данных для поиска
 *
 * @package Techart\SiteSearch\Engine\Mysql
 */
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
			$this->index($item);
		} else {
			$this->delete($item);
		}
	}

	/**
	 * @param Model|Searchable $modelItem
	 * @throws \Exception
	 */
	public function index($modelItem)
	{
		foreach ($this->variants() as $variant) {
			$indexItem = $this->findIndexItemByModelItem($modelItem, $variant);
			if (!$indexItem) {
				$indexItem = $this->createIndexItem();
			}
			$this->assignModelToIndexItem($modelItem, $indexItem, $variant);
			$indexItem->save();
		}
	}

	/**
	 * @param Model|Searchable $modelItem
	 * @throws \Exception
	 */
	public function delete($modelItem)
	{
		foreach ($this->variants() as $variant) {
			$indexItem = $this->findIndexItemByModelItem($modelItem, $variant);
			if ($indexItem) {
				$indexItem->delete();
			}
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
	protected function findIndexItemByModelItem($model, $variant = false)
	{
		return $this->indexItemModel->byModel($model)->variant($variant)->first();
	}

	/**
	 * @return Model
	 */
	protected function createIndexItem()
	{
		return $this->indexItemModel->newInstance();
	}

	/**
	 * @param Model|Searchable $model
	 * @param IndexItem $indexItem
	 * @param bool|string $variant
	 * @throws \TAO\Fields\Exception\UndefinedField
	 */
	protected function assignModelToIndexItem($model, $indexItem, $variant = false)
	{
		$indexItem->field('datatype_code')->set($model->getDatatype());
		$indexItem->field('model_key')->set($model->getKey());

		$indexItem->field('variant')->set($variant ?: '');
		$indexItem->field('title')->set($model->getSearchableTitle($variant));
		$indexItem->field('url')->set($model->getSearchableUrl($variant));
		$indexItem->field('content')->set($model->getSearchableContent($variant));
		$indexItem->field('extra')->set($model->getSearchableExtraData($variant));
	}

	protected function component()
	{
		return app(SiteSearch::class);
	}

	/**
	 * @return array
	 */
	protected function variants()
	{
		return array_keys(\TAO::getVariants());
	}
}

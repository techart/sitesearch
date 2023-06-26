<?php

namespace Techart\SiteSearch\Engine\Mysql\Model;

use Techart\SiteSearch\Engine\Mysql\MySqlIndexItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TAO\ORM\Model;

/**
 * Class IndexItem
 *
 * Модель таблицы данных для поиска
 *
 * @package Techart\SiteSearch\Engine\Mysql\Model
 *
 * @method Builder byModel(Model $model)
 * @method Builder datatype(string $datatypeCode)
 */
class IndexItem extends Model implements MySqlIndexItem
{
	protected $table = 'search_site_index';

	public $adminMenuSection = false;

	/**
	 * @param Builder $query
	 * @param Model $model
	 * @return Builder
	 */
	public function scopeByModel($query, $model)
	{
		return $query
			->datatype($model->getDatatype())
			->where('model_key', $model->getKey())
		;
	}

	/**
	 * @param Builder $query
	 * @param string $datatypeCode
	 * @return Builder
	 */
	public function scopeDatatype($query, $datatypeCode)
	{
		return $query
			->where('datatype_code', $datatypeCode)
		;
	}

	/**
	 * @param Builder $query
	 * @param bool|string $variant
	 * @return Builder
	 */
	public function scopeVariant($query, $variant)
	{
		if (false !== $variant) {
			return $query->where('variant', $variant);
		}
		return $query;
	}

	/**
	 * @return array
	 */
	public function fields()
	{
		$variants = \TAO::getVariants();
		$variant_items = array_combine(
			array_keys($variants),
			array_map(function ($e) { return $e['label'] ?? 'По умолчанию'; }, $variants)
		);

		return [
			'variant' => [
				'label' => 'Вариант контента',
				'type' => 'select(string32) index',
				'default' => 'default',
				'items' => $variant_items,
			],
			'title' => [
				'label' => 'Заголовок материала',
				'type' => 'string(250) fulltext(title, content)',
			],
			'url' => [
				'label' => 'URL материала',
				'type' => 'string(250)',
			],
			'content' => [
				'label' => 'Поисковый контент материала',
				'type' => 'text(long)',
			],
			'extra' => [
				'label' => 'Дополнительные данные материала',
				'type' => 'text(long)',
			],
			'datatype_code' => [
				'label' => 'Тип данных (модель)',
				'type' => 'string(50) index(datatype_code, model_key)',
			],
			'model_key' => [
				'label' => 'ID записи типа данных',
				'type' => 'string(36)',
			],
		];
	}

	public function info()
	{
		return substr($this->field('extra')->value(), 0, 200);
	}

	public function description()
	{
		return substr($this->field('content')->value(), 0, 200);
	}

	public function url()
	{
		return $this->field('url')->value();
	}

	public function title()
	{
		return $this->field('title')->value();
	}

	public function model()
	{
		if ($this->datatype_code() && $this->model_key()) {
			$model = \TAO::datatype($this->datatype_code())->find($this->model_key());
		}
		return $model;
	}

	public function datatype_code()
	{
		return $this->field('datatype_code')->value();
	}

	public function model_key()
	{
		return $this->field('model_key')->value();
	}

	public function searchableFields()
	{
		return Collection::make(['title', 'content']);
	}
}

<?php

namespace Techart\SiteSearch\Engine\Mysql\Model;

use Techart\SiteSearch\Engine\Mysql\MySqlIndexItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use TAO\ORM\Model;

/**
 * Class IndexItem
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
	 *
	 * @return Builder
	 */
	public function scopeByModel($query, $model)
	{
		return $query->datatype($model->getDatatype())->where('model_id', $model->getKey());
	}

	public function scopeDatatype($query, $datatypeCode)
	{
		return $query->where('datatype_code', $datatypeCode);
	}

	public function fields()
	{
		return [
			'title' => [
				'label' => 'Заголовок',
				'type' => 'string(250) fulltext(title, content)',
			],
			'url' => [
				'label' => 'Url',
				'type' => 'string(250)',
			],
			'content' => [
				'label' => 'Контент',
				'type' => 'text(long)',
			],
			'extra' => [
				'label' => 'Дополнительные данные',
				'type' => 'text(long)',
			],
			'datatype_code' => [
				'label' => 'Дататип',
				'type' => 'string(50) index(datatype_code, model_id)',
			],
			'model_id' => [
				'label' => 'Дататип',
				'type' => 'integer',
			],
		];
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
		if ($this->datatype_code() && $this->model_id()) {
			$model = \TAO::datatype($this->datatype_code())->find($this->model_id());
		}
		return $model;
	}

	public function datatype_code()
	{
		return $this->field('datatype_code')->value();
	}

	public function model_id()
	{
		return $this->field('model_id')->value();
	}

	public function searchableFields()
	{
		return Collection::make(['title', 'content']);
	}
}

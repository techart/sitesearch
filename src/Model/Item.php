<?php

namespace Techart\SiteSearch\Model;

use TAO\Fields\Model;

class Item extends Model
{
	public function fields()
	{
		return [
			'datatype_code' => [
				'label' => 'Дататип модели',
				'type' => 'string(50) index(datatype_code, model_id)',
			],
			'model_id' => [
				'label' => 'ID модели',
				'type' => 'integer',
			],
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
		];
	}


}
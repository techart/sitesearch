<?php

namespace Techart\SiteSearch;

use Techart\SiteSearch\Contract\Engine;
use TAO\Fields\Model;

class ModelObserver
{
	/**
	 * @param \TAO\ORM\Model|Searchable $model
	 */
	public function deleted($model)
	{
		$model->deleteFromSearchIndex();
	}

	/**
	 * @param \TAO\ORM\Model|Searchable $model
	 */
	public function saved($model)
	{
		$model->updateSearchIndex();
	}

	/**
	 * @param \TAO\ORM\Model|Searchable $model
	 */
	public function created($model)
	{
		$model->updateSearchIndex();
	}
}
<?php

namespace Techart\SiteSearch\Contract;

use TAO\Fields\Model;

interface Indexer
{
	/**
	 * @param Model $model
	 */
	public function update($model);

	/**
	 * @param Model $model
	 */
	public function delete($model);
}

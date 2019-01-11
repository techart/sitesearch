<?php

namespace Techart\SiteSearch\Contract;

use TAO\Fields\Model;

interface Indexer
{
	/**
	 * @param Model $item
	 */
	public function update($item);

	/**
	 * @param Model $model
	 */
	public function delete($model);

	public function clear();
}

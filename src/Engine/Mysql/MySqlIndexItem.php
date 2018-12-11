<?php

namespace Techart\SiteSearch\Engine\Mysql;

use Techart\SiteSearch\Contract\IndexItem;
use Illuminate\Support\Collection;

interface MySqlIndexItem extends IndexItem
{
	/**
	 * @return Collection
	 */
	public function searchableFields();
}
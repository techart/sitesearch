<?php

namespace Techart\SiteSearch\Contract;

use Illuminate\Database\Eloquent\Collection;

interface Engine
{
	public function initialize();

	/**
	 * @param string $query
	 * @return Collection
	 */
	public function search($query);

	/**
	 * @return Indexer
	 */
	public function indexer();
}
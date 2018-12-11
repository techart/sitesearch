<?php

namespace Techart\SiteSearch;

use TAO\ORM\Model;
use Techart\SiteSearch\Contract\Engine;

class SiteSearch
{
	protected $view;

	public function initialize()
	{
		$this->engine()->initialize();
	}

	/**
	 * @return Engine
	 */
	public function engine()
	{
		return $this->engineManager()->driver();
	}

	public function engineManager()
	{
		return app(EngineManager::class);
	}

	public function view()
	{
		if (!$this->view) {
			$this->view = new View();
		}
		return $this->view;
	}

	/**
	 * @param Model|Searchable $model
	 * @return bool
	 */
	public function isSearchableModel($model)
	{
		return trait_used($model, Searchable::class);
	}
}
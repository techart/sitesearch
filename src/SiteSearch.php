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
		return new EngineManager(app());
//		return app(EngineManager::class);
	}

	public function clearIndex()
	{
		$this->engine()->indexer()->clear();
	}

	public function view()
	{
		if (!$this->view) {
			$this->view = new View();
		}
		return $this->view;
	}

	/**
	 * @param Model $model
	 * @return bool
	 */
	public function isSearchableDatatype($model)
	{
		if (!$model->isDatatype()) {
			return $this->isSearchableItem($model);
		}
		return trait_used($model, Searchable::class);
	}

	/**
	 * @param Model|Searchable $model
	 * @return bool
	 */
	public function isSearchableItem($model)
	{
		return $this->isSearchableDatatype($model->getDatatypeObject()) && $model->isSearchableItem();
	}
}

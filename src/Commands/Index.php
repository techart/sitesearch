<?php

namespace Techart\SiteSearch\Commands;

use Illuminate\Console\Command;
use TAO\ORM\Model;
use Techart\SiteSearch\Searchable;
use Techart\SiteSearch\SiteSearch;

class Index extends Command
{
	protected $signature = 'sitesearch:index {datatypeCode?}';

	protected $description = 'Индексирует записи переданного дататипа';

	public function handle()
	{
		foreach ($this->getRequiredDatatypes() as $datatype) {
			$datatype->updateSearchIndex();
		}
	}

	protected function getRequiredDatatypes()
	{
		/** @var Model[]|Searchable[] $datatypes */
		if (is_null($this->argument('datatypeCode'))) {
			$datatypes = $this->getAllSearchableDatatype();
		} else {
			$datatypes[] = \TAO::datatype($this->argument('datatypeCode'));
		}
		return $datatypes;
	}

	protected function getAllSearchableDatatype()
	{
		return array_filter(\TAO::datatypes(), function ($datatype) {
			return $this->component()->isSearchableModel($datatype);
		});
	}

	protected function component()
	{
		return app(SiteSearch::class);
	}
}
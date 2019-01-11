<?php

namespace Techart\SiteSearch;

class IndexManager
{
	public function reindex($datatypeCode = null)
	{
		$this->component()->clearIndex();
		foreach ($this->getRequiredDatatypes($datatypeCode) as $datatype) {
			$datatype->updateSearchIndex();
		}
	}

	protected function getRequiredDatatypes($datatypeCode = null)
	{
		/** @var Model[]|Searchable[] $datatypes */
		if (is_null($datatypeCode)) {
			$datatypes = $this->getAllSearchableDatatype();
		} else {
			$datatypes[] = \TAO::datatype($datatypeCode);
		}
		return $datatypes;
	}

	protected function getAllSearchableDatatype()
	{
		return array_filter(\TAO::datatypes(), function ($datatype) {
			return $this->component()->isSearchableDatatype($datatype);
		});
	}

	protected function component()
	{
		return app(SiteSearch::class);
	}
}

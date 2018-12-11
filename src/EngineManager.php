<?php

namespace Techart\SiteSearch;

use Illuminate\Support\Manager;
use Techart\SiteSearch\Engine\Mysql;

class EngineManager extends Manager
{
	public function createMysqlDriver()
	{
		return app(Mysql::class);
	}

	public function getDefaultDriver()
	{
		return config('sitesearch.engine') ?: 'mysql';
	}
}
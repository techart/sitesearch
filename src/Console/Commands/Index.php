<?php

namespace Techart\SiteSearch\Console\Commands;

use Illuminate\Console\Command;
use Techart\SiteSearch\IndexManager;

class Index extends Command
{
	protected $signature = 'sitesearch:index {datatypeCode?}';

	protected $description = 'Индексирует записи переданного дататипа или всех, если не указан конкретный';

	public function handle()
	{
		app(IndexManager::class)->reindex();
	}
}

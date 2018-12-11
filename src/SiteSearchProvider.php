<?php

namespace Techart\SiteSearch;

use TAO\Support\ComponentServiceProvider;

class SiteSearchProvider extends ComponentServiceProvider
{
	public function mnemocode()
	{
		return 'sitesearch';
	}

	protected function packageDir()
	{
		return realpath(__DIR__ . '/../');
	}

	protected function namespace()
	{
		return __NAMESPACE__;
	}

	public function register()
	{
		$this->app->singleton('sitesearch', SiteSearch::class);

		$this->app->singleton(EngineManager::class, function ($app) {
			return new EngineManager($app);
		});
	}

	public function boot()
	{
		parent::boot();

		$this->app->sitesearch->initialize();
	}
}
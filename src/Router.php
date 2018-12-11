<?php

namespace Techart\SiteSearch;

class Router
{
	public function route()
	{
		\Route::get('/search/', '\\Techart\\SiteSearch\\Controller@index')->name('search_result');
	}
}
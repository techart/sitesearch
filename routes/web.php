<?php

Route::get(
	config('sitesearch.result_url'), '\\Techart\\SiteSearch\\Controller@index'
)->name('search_result');

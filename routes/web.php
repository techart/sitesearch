<?php

Route::get(
	config('sitesearch.result_url'), '\\Techart\\SiteSearch\\Controller@index'
)->name('search_result');

Route::get(
	config('sitesearch.result_page_url'), '\\Techart\\SiteSearch\\Controller@index'
)->where('page', '[0-9]+')->name('search_result_page');

<?php

namespace Techart\SiteSearch\Facade;

use Techart\SiteSearch\Contract\Engine;
use Illuminate\Support\Facades\Facade;

/**
 * Class SiteSearch
 * @package Techart\SiteSearch\Facade
 *
 * @method Engine engine()
 */
class SiteSearch extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'sitesearch';
	}

}
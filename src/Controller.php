<?php

namespace Techart\SiteSearch;

use Techart\SiteSearch\Facade\SiteSearch;

class Controller extends \TAO\Controller
{
	public function index()
	{
		$query = $this->getQueryFromRequest();
		$resultItems = null;
		$message = '';
		if ($query) {
			$resultItems = SiteSearch::engine()->search($query)->get();
			if ($resultItems->count() === 0) {
				$message = trans('sitesearch::messages.empty_result');
			}
		} else {
			$message = trans('sitesearch::messages.empty_query');
		}

		return view('sitesearch.results', [
			'resultItems' => $resultItems,
			'message' => $message
		]);
	}

	protected function getQueryFromRequest()
	{
		return request('q');
	}
}


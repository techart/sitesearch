<?php

namespace Techart\SiteSearch;

class Controller extends \TAO\Controller
{
	public function index($page = 1)
	{
		$queryString = $this->getQueryFromRequest();
		$resultItems = null;
		$numPages = 1;
		$message = '';
		if ($queryString) {
			$result = app('sitesearch')->engine()->search($queryString);
			$resultTotalCount = $result->count();
			$perPage = config('sitesearch.result_per_page');
			if ($resultTotalCount > 0) {
				$numPages = ceil($resultTotalCount / $perPage);
				$resultItems = $result->limit($perPage)->offset(($page - 1) * $perPage)->get();
			}

			if ($resultItems->count() === 0) {
				if ($page > 1) {
					return $this->pageNotFound();
				} else {
					$message = trans('sitesearch::messages.empty_result');
				}
			}
		} else {
			$message = trans('sitesearch::messages.empty_query');
		}

		return view('sitesearch.results', [
			'resultItems' => $resultItems,
			'message' => $message,
			'numpages' => $numPages,
			'page' => $page,
			'pager_callback' => function ($page) use ($queryString) {
				$url = route('search_result_page', ['page' => $page, 'q' => $queryString]);
				if (\URL::hasTrailingSlash(config('sitesearch.result_page_url')) && !\URL::hasTrailingSlash($url)) {
					$url = \URL::addTrailingSlash($url);
				}
				return $url;
			}
		]);
	}

	protected function getQueryFromRequest()
	{
		return request('q');
	}
}


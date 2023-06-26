<?php

namespace Techart\SiteSearch;

/**
 * Class Controller
 *
 * Контроллер вывода результатов поиска по умолчанию
 *
 * Для изменения логики работы контроллера можете переопределить контроллер своим дочерним классом
 * и указать свой класс как обработчик запросов к страницам в таблице маршрутов сайта (routes/web.php)
 *
 * @package Techart\SiteSearch
 */
class Controller extends \TAO\Controller
{
	/**
	 * Метод получения и вывода результатов поиска
	 *
	 * @param int $page
	 * @return Illuminate\Contracts\View\View
	 */
	public function index($page = 1)
	{
		$queryString = $this->getQueryFromRequest();
		$resultItems = null;
		$numPages = 1;
		$message = '';

		if ($queryString) {
			$result = app('sitesearch')->engine()->search($queryString, $this->getSearchVariant());
			$resultTotalCount = $result->count();
			$perPage = config('sitesearch.result_per_page');
			if ($resultTotalCount > 0) {
				$numPages = ceil($resultTotalCount / $perPage);
				$resultItems = $result->limit($perPage)->offset(($page - 1) * $perPage)->get();
			}

			if ($resultTotalCount === 0) {
				if ($page > 1) {
					return $this->pageNotFound();
				} else {
					$message = trans('sitesearch::messages.empty_result');
				}
			}
		} else {
			$message = trans('sitesearch::messages.empty_query');
		}

		return view('sitesearch::results', [
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

	/**
	 * Метод получения поискового запроса
	 *
	 * @return string
	 */
	protected function getQueryFromRequest()
	{
		$searchParameter = config('sitesearch.search_query_parameter', 'q');
		return $this->prepareSearchQuery(request()->query($searchParameter));
	}

	/**
	 * Метод обработки пользовательского поискового запроса
	 *
	 * @param string $query
	 * @return string
	 */
	protected function prepareSearchQuery($query)
	{
		return filter_var($query, FILTER_SANITIZE_STRING);
	}

	/**
	 * Метод получения текущего варианта контента для поиска
	 *
	 * @return string
	 */
	protected function getSearchVariant()
	{
		return \TAO::getVariant();
	}

}


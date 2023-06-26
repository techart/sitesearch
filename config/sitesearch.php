<?php
/**
 * Настройка работы пакета поиска "по умолчанию"
 *
 * Вы можете переопределять эти параметры своими значениям
 * в файле config/sitesearch.php вашего проекта
 */
return [
	// Используемая база данных (пока доступна только MySQL - mysql)
	'engine' => 'mysql',
	// Дополнительные настройки режима поиска
	'mysql' => [
		// Режим поиска по полнотекстовому индексу:
		// * boolean				- поиск IN BOOLEAN MODE
		// * natural (не boolean)	- поиск IN NATURAL LANGUAGE MODE или WITH QUERY EXPANSION (если в конфиге установлен параметр query_expansion)
		'mode' => 'boolean',
	],
	// Имя GET-переменной, через которую присылается поисковый запрос
	'search_query_parameter' => 'q',
	// URL страницы с результатами поиска
	'result_url' => '/search',
	// URL постраничной навигации по результатам поиска
	'result_page_url' => '/search/page-{page}',
	// Количество позиций на странице результатов поиска
	'result_per_page' => 20,
];

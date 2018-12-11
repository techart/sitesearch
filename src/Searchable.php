<?php

namespace Techart\SiteSearch;

use Illuminate\Database\Eloquent\Collection;
use TAO\ORM\Model;
use Techart\SiteSearch\Facade\SiteSearch;
use TAO\Fields\Field;

trait Searchable
{
	protected $searchableContentSeparator = ' ';

	public static function bootSearchable()
	{
		self::observe(ModelObserver::class);
	}

	/**
	 * Возвращает url-адрес записи для результатов поиска
	 *
	 * @return string
	 */
	public function searchableUrl()
	{
		return $this->url();
	}

	/**
	 * Возвращает заголовок записи для осуществления поиска и вывода результатов поиска
	 *
	 * @return string
	 */
	public function searchableTitle()
	{
		return $this->title();
	}

	/**
	 * Возвращает контент записи для осуществления поиска и вывода результатов поиска.
	 *
	 * @return string
	 */
	public function searchableContent()
	{
		$content = '';
		foreach ($this->searchableFields() as $field) {
			$content .= $field->value() . $this->searchableContentSeparator;
		}
		return $this->prepareSearchableContent(rtrim($content, $this->searchableContentSeparator));
	}

	/**
	 * Возвращает поля записи, значения которых должны участвовать в поиске. По умолчанию собирает все поля, у которых
	 * в настройках есть пункт 'searchable' => true.
	 *
	 * @return Field[]
	 */
	public function searchableFields()
	{
		$fields = [];
		foreach ($this->fields() as $fieldName => $fieldParams) {
			if (isset($fieldParams['searchable']) && $fieldParams['searchable']) {
				$fields[$fieldName] = $this->field($fieldName);
			}
		}
		return $fields;
	}

	/**
	 * Возвращает дополнительную информацию о записи. Не участвует в поиске, но может использоваться, например,
	 * при выводе результатов поиска.
	 *
	 * @return string
	 */
	public function searchableExtraData()
	{
		return '';
	}

	/**
	 * Обрабатывает контент для приведения его к виду, удобному для использования в поиске (удаление тегов,
	 * предлогов и тд).
	 *
	 * @param $content
	 * @return string
	 */
	public function prepareSearchableContent($content)
	{
		$content = preg_replace('{&[a-z0-9#]+;}i', ' ', $content);
		$content = preg_replace('{<noindex>.+?</noindex>}ism', '', $content);
		$content = preg_replace('{<ssnoindex>.+?</ssnoindex>}ism', '', $content);
		return strip_tags($content);
	}

	/**
	 * Если экземлпяр является дататипом, то индексирует все его записи, доступные для этого. Если экзмепляр - запись,
	 * то индексирует только эту запись.
	 */
	public function updateSearchIndex()
	{
		/** @var Model $this */
		if ($this->isDatatype()) {
			foreach ($this->itemsForSearch() as $item) {
				$item->updateSearchIndex();
			}
		} else {
			$this->searchEngine()->indexer()->update($this);
		}
	}

	/**
	 * Удаляет текущую запись из поискового индекса.
	 */
	public function deleteFromSearchIndex()
	{
		$this->searchEngine()->indexer()->delete($this);
	}

	/**
	 * Возвращает используемый движок поиска для управления индексом записей.
	 *
	 * @return Contract\Engine
	 */
	protected function searchEngine()
	{
		return app(\Techart\SiteSearch\SiteSearch::class)->engine();
	}

	/**
	 * Возварщает список записей текущего типа данных, доступных для поиска.
	 *
	 * @return Collection
	 */
	protected function itemsForSearch()
	{
		return $this->getAccessibleItems()->get();
	}

}
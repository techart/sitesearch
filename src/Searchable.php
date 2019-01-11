<?php

namespace Techart\SiteSearch;

use Illuminate\Database\Eloquent\Collection;
use TAO\ORM\Model;
use TAO\Fields\Field;

trait Searchable
{
	protected $searchableContentSeparator = ' ';
	protected $searchableItems;
	protected $searchableItemsKeys;

	public static function bootSearchable()
	{
		self::observe(ModelObserver::class);
	}

	/**
	 * Возвращает url-адрес записи для результатов поиска
	 *
	 * @return string
	 */
	public function getSearchableUrl()
	{
		return $this->url();
	}

	/**
	 * Возвращает заголовок записи для осуществления поиска и вывода результатов поиска
	 *
	 * @return string
	 */
	public function getSearchableTitle()
	{
		return $this->title();
	}

	/**
	 * Возвращает контент записи для осуществления поиска и вывода результатов поиска.
	 *
	 * @return string
	 */
	public function getSearchableContent()
	{
		$content = '';
		foreach ($this->getSearchableFields() as $field) {
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
	public function getSearchableFields()
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
	public function getSearchableExtraData()
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
		return strip_tags($content);
	}

	/**
	 * Если экземлпяр является дататипом, то индексирует все его записи, доступные для этого. Если экзмепляр - запись,
	 * то индексирует только эту запись.
	 */
	public function updateSearchIndex()
	{
		$this->deleteFromSearchIndex();
		/** @var Model $this */
		if ($this->isDatatype()) {
			foreach ($this->getSearchableItems(false) as $item) {
				$item->updateSearchIndex();
			}
		} else {
			$this->getSearchEngine()->indexer()->update($this);
		}
	}

	/**
	 * Если экземлпяр является дататипом, то удаляет из индекса все его записи, доступные для этого.
	 * Если экзмепляр - запись, то удаляет только эту запись.
	 */
	public function deleteFromSearchIndex()
	{
		/** @var Model $this */
		if ($this->isDatatype()) {
			foreach ($this->get() as $item) {
				$item->deleteFromSearchIndex();
			}
		} else {
			$this->getSearchEngine()->indexer()->delete($this);
		}
	}

	/**
	 * Возвращает используемый движок поиска для управления индексом записей.
	 *
	 * @return Contract\Engine
	 */
	protected function getSearchEngine()
	{
		return app(\Techart\SiteSearch\SiteSearch::class)->engine();
	}

	/**
	 * Возвращает список записей текущего типа данных, доступных для поиска.
	 *
	 * @param bool $cacheEnabled
	 * @return Collection
	 */
	protected function getSearchableItems($cacheEnabled = true)
	{
		if ($cacheEnabled && !is_null($this->searchableItems)) {
			return $this->searchableItems;
		} else {
			$items = $this->getAccessibleItems()->get();
			return $cacheEnabled ? ($this->searchableItems = $items) : $items;
		}
	}

	protected function getSearchableItemKeys($cacheEnabled = true)
	{
		if ($cacheEnabled && !is_null($this->searchableItemsKeys)) {
			return $this->searchableItemsKeys;
		} else {
			$keys = $this->getAccessibleItems()->pluck($this->getKeyName());
			return $cacheEnabled ? ($this->searchableItemsKeys = $keys) : $keys;
		}
	}

	/**
	 * Проверяет доступна ли запись для поиска
	 *
	 * @return bool
	 */
	public function isSearchableItem()
	{
		return $this->getDatatypeObject()->getSearchableItemKeys()->contains($this->getKey());
	}

}

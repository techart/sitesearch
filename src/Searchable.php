<?php

namespace Techart\SiteSearch;

use Illuminate\Database\Eloquent\Collection;
use TAO\ORM\Model;
use TAO\Fields\Field;

/**
 * Trait Searchable
 *
 * Примесь к классам моделей, записи (или специальные материалы) которых
 * требуется находить пользовательским поиском по сайту 
 *
 * После подключения примеси к классу модели укажите в свойствах полей модели
 * параметр searchable со значением true, чтобы их значения участвовали в поиске.
 *
 * Поле title по умолчанию участвует в поиске автоматически (см. метод getSearchableTitle()).
 *
 * @package Texhart\SiteSearch
 */
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
	 * Принимает в качестве параметра название варианта контента (языка, региона и т.п.)
	 *
	 * @param string $variant
	 * @return string
	 */
	public function getSearchableUrl($variant = false)
	{
		return $this->url();
	}

	/**
	 * Возвращает заголовок результата поиска
	 *
	 * Принимает в качестве параметра название варианта контента (языка, региона и т.п.)
	 *
	 * @param string $variant
	 * @return string
	 */
	public function getSearchableTitle($variant = false)
	{
		$title = $this->getKey();
		if ($field = $this->field('title')) {
			if ($variant && $field instanceof \TAO\Fields\MultivariantField) {
				$title = $field->variantValue($variant);
			} else {
				$title = $field->value();
			}
		}
		return $this->prepareSearchableContent($title);
	}

	/**
	 * Возвращает контент записи для осуществления поиска и вывода результатов поиска
	 *
	 * Принимает в качестве параметра название варианта контента (языка, региона и т.п.)
	 *
	 * @return string
	 */
	public function getSearchableContent($variant = false)
	{
		$content = '';
		foreach ($this->getSearchableFields() as $field) {
			if ($variant && $field instanceof \TAO\Fields\MultivariantField) {
				$value = $field->variantValue($variant);
			} else {
				$value = $field->value();
			}
			$content .= $value . $this->searchableContentSeparator;
		}
		return $this->prepareSearchableContent(rtrim($content, $this->searchableContentSeparator));
	}

	/**
	 * Возвращает поля записи, значения которых должны участвовать в поиске
	 *
	 * По умолчанию собирает все поля, у которых в настройках есть пункт 'searchable' => true.
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
	 * Возвращает дополнительную информацию о записи
	 *
	 * Не участвует в поиске, но может использоваться, например, при выводе результатов поиска.
	 *
	 * @return string
	 */
	public function getSearchableExtraData($variant = false)
	{
		return '';
	}

	/**
	 * Подготавливает контент для использования в поиске
	 *
	 * Удаляет из контента:
	 * * HTML-сущности
	 * * HTML-теги
	 * * внутреннее содержимое тегов title, noindex, script, style
	 * * множественные пробелы и переводы строк
	 *
	 * @param $content
	 * @return string
	 */
	public function prepareSearchableContent($content)
	{
		$content = preg_replace('{&[a-z0-9#]+;}i', ' ', $content);
		$content = preg_replace('{<title>.+?</title>}ism', '', $content);
		$content = preg_replace('{<noindex>.+?</noindex>}ism', '', $content);
		$content = preg_replace('{<script(?:[^>]+)?>.+?</script>}ism', '', $content);
		$content = preg_replace('{<style(?:[^>]+)?>.+?</style>}ism', '', $content);
		return trim(preg_replace('{\s+}', ' ', strip_tags($content)));
	}

	/**
	 * Обработка обновления данных
	 *
	 * Если экземпляр является типом данных, то индексирует все его записи, доступные для поиска.
	 * Если экземпляр - отдельная запись, то индексирует только эту запись.
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
	 * Обработка удаления данных
	 *
	 * Если экземпляр является типом данных, то удаляет из индекса все его записи, доступные для поиска.
	 * Если экземпляр - отдельная запись, то удаляет только эту запись.
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
	 * Возвращает используемый движок поиска для управления индексом записей
	 *
	 * @return Contract\Engine
	 */
	protected function getSearchEngine()
	{
		return app(\Techart\SiteSearch\SiteSearch::class)->engine();
	}

	/**
	 * Возвращает список записей текущего типа данных, доступных для поиска
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

	/**
	 * Возвращает список ID записей текущего типа данных, доступных для поиска
	 *
	 * @param bool $cacheEnabled
	 * @return Collection
	 */
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
	 * Проверяет, доступна ли запись для поиска
	 *
	 * @return bool
	 */
	public function isSearchableItem()
	{
		return $this->getDatatypeObject()->getSearchableItemKeys()->contains($this->getKey());
	}

}

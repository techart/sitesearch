<?php

namespace Techart\SiteSearch\Text;

use Techart\SiteSearch\Exception;
use TAO\Callback;
use Wamania\Snowball\Russian;

/**
 * Class Stemmer
 *
 * Класс позволяет заменить слова в тексте на их стеммы.
 *
 * @package Techart\SiteSearch\Text
 */
class Stemmer
{
	protected $service;

	/**
	 * Заменяет слова в строке str на соответствющие стеммы. В аругмент $exceptions можно передать исключения в виде
	 * regexp или callable параметра, а так же их массива. Если передан $wordProcessCallback
	 *
	 * @param string $str
	 * @param array|string|Callback|callable $exceptions
	 * @param Callback|callable $wordProcessCallback
	 * @return string
	 */
	public function process($str, $exceptions = null, $wordProcessCallback = null)
	{
		return $this->normalizeSpaceSymbols(
			$this->replaceWordsToStemms(
				$this->normalizeSpaceSymbols($str),
				$exceptions,
				$wordProcessCallback
			)
		);
	}

	protected function processWord($word, $exceptions = null, $wordProcessCallback = null)
	{
		$word = $this->normalizeWord($word);
		if ($word) {
			if (!$this->isException($word, $exceptions)) {
				$stem = $this->service()->stem($word);
				if (Callback::isValidCallback($wordProcessCallback)) {
					$stem = Callback::instance($wordProcessCallback)->call($word, $stem);
				}
				return $stem;
			}
		}
		return $word;
	}

	protected function normalizeSpaceSymbols($str)
	{
		return preg_replace('{\s+}u', ' ', $str);
	}

	protected function replaceWordsToStemms($query, $exceptions = null, $wordProcessCallback = null)
	{
		return preg_replace_callback("{[^\s]+}u",
			function($matches) use ($exceptions, $wordProcessCallback) {
				return $this->processWord($matches[0], $exceptions, $wordProcessCallback);
			},
			$query
		);
	}

	protected function service()
	{
		if (is_null($this->service)) {
			$this->service = new Russian();
		}
		return $this->service;
	}

	protected function isException($word, $exceptions = null)
	{
		if (is_null($exceptions)) {
			$res = false;
		} else if (is_string($exceptions)) {
			$res = preg_match($exceptions, $word);
		} else if (is_array($exceptions)) {
			$res = false;
			foreach ($exceptions as $exception) {
				if ($this->isException($word, $exception)) {
					$res = true;
					break;
				}
			}
		} else if (Callback::isValidCallback($exceptions)) {
			$res = Callback::instance($exceptions)->call($word);
		} else {
			throw new Exception("Invalid exceptions for stemmer: '$exceptions'");
		}
		return $res;
	}

	protected function normalizeWord($word)
	{
		$word = trim($word);
		$word = preg_replace('{[^а-яa-zё0-9]+$}iu', '', $word);
		return $word;
	}

}
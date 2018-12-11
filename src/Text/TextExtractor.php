<?php

namespace Techart\SiteSearch\Text;

class TextExtractor
{
	/**
	 * @var string $str
	 */
	protected $str;
	/**
	 * @var array $extractedTexts
	 */
	protected $extractedTexts = [];

	public function __construct($str = null)
	{
		$this->str = $str;
	}

	public function extract($regexp)
	{
		$texts = $this->extractedTexts;
		$index = count($texts);
		$this->str = preg_replace_callback($regexp, function($matches) use (&$texts, &$index) {
			$texts[$index] = $matches[0];
			$label = $this->extractedTextLabel($index);
			$index++;
			return $label;
		}, $this->str);
		$this->extractedTexts = $texts;
		return $this;
	}

	/**
	 * @return string
	 */
	public function returnTexts()
	{
		$texts = $this->extractedTexts();
		$this->setStr(preg_replace_callback($this->extractedTextLabelRegexp(), function($matches) use ($texts) {
			return $texts[$matches[1]];
		}, $this->str()));
		return $this;
	}

	public function str()
	{
		return $this->str;
	}

	public function setStr($str)
	{
		$this->str = $str;
		return $this;
	}

	public function extractedTexts()
	{
		return $this->extractedTexts;
	}

	public function extractedTextLabelRegexp()
	{
		return "{\{:text(\d+)\}}";
	}

	protected function extractedTextLabel($index)
	{
		return "{:text$index}";
	}

	public function __toString()
	{
		return $this->str();
	}
}
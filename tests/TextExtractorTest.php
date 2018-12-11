<?php

namespace Techart\SiteSearchTests;

use Techart\SiteSearch\Text\TextExtractor;

class TextExtractorTest extends TestCase
{
	/**
	 * @var TextExtractor $extractor
	 */
	protected $extractor;

	protected function setUp()
	{
		$this->extractor = app(TextExtractor::class);
		parent::setUp();
	}

	protected function tearDown()
	{
		$this->extractor = null;
		parent::tearDown();
	}


	public function testSimpleExtract()
	{
		$str = 'Тестовая "строка"';
		$regexp = "{\"[^\"]+\"}";
		/** @var \App\SiteSearch\Text\TextExtractor $extractor */
		$this->extractor->setStr($str)->extract($regexp);

		$this->assertContains('Тестовая', (string)$this->extractor);
		$this->assertNotContains('строка', (string)$this->extractor);

		$this->extractor->returnTexts();
		$this->assertContains($str, (string)$this->extractor);
	}

	public function testMultiplyExtract()
	{
		$str = 'Тестовая "строка", а потом "еще одна"';
		$regexp = "{\"[^\"]+\"}";
		/** @var \App\SiteSearch\Text\TextExtractor $extractor */
		$this->extractor->setStr($str)->extract($regexp);

		$this->assertContains('Тестовая', (string)$this->extractor);
		$this->assertNotContains('строка', (string)$this->extractor);
		$this->assertNotContains('еще одна', (string)$this->extractor);

		$this->extractor->returnTexts();
		$this->assertContains($str, (string)$this->extractor);
	}
}

<?php

namespace Techart\SiteSearchTests;

use Techart\SiteSearch\Text\Stemmer;

class StemmerTest extends TestCase
{
	/**
	 * @var Stemmer
	 */
	protected $stemmer;

	protected function setUp()
	{
		$this->stemmer = app(Stemmer::class);
		parent::setUp();
	}

	protected function tearDown()
	{
		$this->stemmer = null;
		parent::tearDown();
	}

	public function testWordStemming()
	{
		$word = 'блоками';
		$processedWord = $this->stemmer->process($word);
		$this->assertEquals('блок', $processedWord);
	}

	public function testWordStemmingExceptions()
	{
		$word = 'блокам*';
		$processedWord = $this->stemmer->process($word, '{\*+}');
		$this->assertEquals($word, $processedWord);
	}

	public function testStemming()
	{
		$word = 'блоками и стрелками';
		$processedWord = $this->stemmer->process($word);
		$this->assertEquals('блок и стрелк', $processedWord);
	}

	public function testStemmingRegexpExceptions()
	{
		$word = 'блоками и стрелками';
		$processedWord = $this->stemmer->process($word, '{^блок}');
		$this->assertEquals('блоками и стрелк', $processedWord);
	}

	public function testStemmingCallbackExceptions()
	{
		$word = 'блоками и стрелками';
		$processedWord = $this->stemmer->process($word, function($val) {
			return $val == 'блоками';
		});
		$this->assertEquals('блоками и стрелк', $processedWord);
	}

	public function testStemmingMultiplyExceptions()
	{
		$word = 'показано блоками и -стрелками';
		$processedWord = $this->stemmer->process($word, [
			function($val) {
				return $val == 'блоками';
			},
			'{^-}'
		]);
		$this->assertEquals('показа блоками и -стрелками', $processedWord);
	}

	public function testStemmingWithProcessCallback()
	{
		$word = 'блоками стрелками';
		$processedWord = $this->stemmer->process($word, null, function ($word, $stem) {
			return "+$stem*";
		});
		$this->assertEquals('+блок* +стрелк*', $processedWord);
		$processedWord = $this->stemmer->process($word, null, function ($word, $stem) {
			return "+$word*";
		});
		$this->assertEquals('+блоками* +стрелками*', $processedWord);
	}
}
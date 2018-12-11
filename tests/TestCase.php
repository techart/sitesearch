<?php

namespace Techart\SiteSearchTests;

abstract class TestCase extends \TaoTests\TestCase
{
	protected function vendorPath()
	{
		return realpath(__DIR__ . '/../vendor');
	}
}
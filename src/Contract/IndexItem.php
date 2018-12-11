<?php

namespace Techart\SiteSearch\Contract;

use TAO\Fields\Model;

interface IndexItem
{
	/**
	 * @return string
	 */
	public function title();

	/**
	 * @return string
	 */
	public function description();

	/**
	 * @return string
	 */
	public function url();

	/**
	 * @return Model
	 */
	public function model();

}
<?php
namespace Shuttle\Interfaces;

use Shuttle\Application;

abstract class IProvider
{
	/** @var Application $app */
	protected $app;

	/**
	 * @param Application $app
	 *
	 * @return void
	 */
	public function __construct(Application &$app)
	{
		$this->app = $app;
	}

	/**
	 * @return void
	 */
	abstract public function load();

	/**
	 * @return string
	 */
	public function getReference()
	{
		return basename(str_replace('\\', '/', get_class($this)));
	}
}
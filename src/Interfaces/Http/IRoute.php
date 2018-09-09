<?php
namespace Shuttle\Interfaces\Http;

/**
 * Class IRoute
 *
 * @package Shuttle\Interfaces\Http
 */
abstract class IRoute
{
	/** @var string[] $methods */
	protected static $methods
		= [
			'get', 'post', 'put', 'patch',
			'delete', 'link', 'unlink',
		];

	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return IMethod
	 */
	abstract public function __call($method, $params);

	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return IMethod
	 */
	abstract public static function __callStatic($method, $params);
}
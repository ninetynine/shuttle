<?php
namespace Shuttle\Http;

use Shuttle\Interfaces\Http\IRoute;

use Shuttle\Http\Methods\Get;
use Shuttle\Http\Methods\Post;
use Shuttle\Http\Methods\Put;
use Shuttle\Http\Methods\Patch;
use Shuttle\Http\Methods\Delete;
use Shuttle\Http\Methods\Link;
use Shuttle\Http\Methods\Unlink;

/**
 * Class Route
 *
 * @method Get get(string $route, $options, $controller = null)
 * @method Post post(string $route, $options, $controller = null)
 * @method Put put(string $route, $options, $controller = null)
 * @method Patch patch(string $route, $options, $controller = null)
 * @method Delete delete(string $route, $options, $controller = null)
 * @method Link link(string $route, $options, $controller = null)
 * @method Unlink unlink(string $route, $options, $controller = null)
 *
 * @package Shuttle\Http
 */
class RouteGroup extends IRoute
{
	/** @var array $options */
	protected $options = [];

	/**
	 * @param array $options
	 */
	public function __construct(array $options = [])
	{
		$this->options = $options;
	}

	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return null|\Shuttle\Interfaces\Http\IMethod
	 */
	public function __call($method, $params)
	{
		if (!in_array($method, static::$methods)) {
			return null;
		}

		$params = array_pad($params, 3, null);

		return Router::initialize()->{$method}(
			$params[ 0 ],
			...$this->mergeOptions($params[ 1 ], $params[ 2 ])
		);
	}

	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return null
	 */
	public static function __callStatic($method, $params)
	{
		return null;
	}

	/**
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return array
	 */
	private function mergeOptions($options, $controller = null)
	{
		return [
			is_array($options)
				? array_merge($this->options, $options)
				: $this->options,
			is_null($controller)
				? $options : $controller,
		];
	}
}
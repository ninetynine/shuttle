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
 * @method static Get get(string $route, $options, $controller = null)
 * @method static Post post(string $route, $options, $controller = null)
 * @method static Put put(string $route, $options, $controller = null)
 * @method static Patch patch(string $route, $options, $controller = null)
 * @method static Delete delete(string $route, $options, $controller = null)
 * @method static Link link(string $route, $options, $controller = null)
 * @method static Unlink unlink(string $route, $options, $controller = null)
 *
 * @package Shuttle\Http
 */
class Route extends IRoute
{
	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return null
	 */
	public function __call($method, $params)
	{
		return null;
	}

	/**
	 * @param string $method
	 * @param array  $params
	 *
	 * @return null|\Shuttle\Interfaces\Http\IMethod
	 */
	public static function __callStatic($method, $params)
	{
		if (!in_array($method, static::$methods)) {
			return null;
		}

		return Router::initialize()->{$method}(...$params);
	}

	/**
	 * @param array    $options
	 * @param \Closure $closure
	 *
	 * @return void
	 */
	public static function group(array $options, \Closure $closure)
	{
		call_user_func($closure, new RouteGroup($options));
	}
}
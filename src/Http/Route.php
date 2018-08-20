<?php
namespace Shuttle\Http;

class Route
{
	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Get
	 */
	public static function get(string $route, $options, $controller = null)
	{
		return Router::initialize()->get($route, $options, $controller);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Post
	 */
	public static function post(string $route, $options, $controller = null)
	{
		return Router::initialize()->post($route, $options, $controller);
	}
}
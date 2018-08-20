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

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Put
	 */
	public static function put(string $route, $options, $controller = null)
	{
		return Router::initialize()->put($route, $options, $controller);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Patch
	 */
	public static function patch(string $route, $options, $controller = null)
	{
		return Router::initialize()->patch($route, $options, $controller);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Delete
	 */
	public static function delete(string $route, $options, $controller = null)
	{
		return Router::initialize()->delete($route, $options, $controller);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Link
	 */
	public static function link(string $route, $options, $controller = null)
	{
		return Router::initialize()->link($route, $options, $controller);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Unlink
	 */
	public static function unlink(string $route, $options, $controller = null)
	{
		return Router::initialize()->unlink($route, $options, $controller);
	}
}
<?php
namespace Shuttle\Http;

class RouteGroup
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
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Get
	 */
	public function get(string $route, $options, $controller = null)
	{
		return Router::initialize()->get(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Post
	 */
	public function post(string $route, $options, $controller = null)
	{
		return Router::initialize()->post(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Put
	 */
	public function put(string $route, $options, $controller = null)
	{
		return Router::initialize()->put(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Patch
	 */
	public function patch(string $route, $options, $controller = null)
	{
		return Router::initialize()->patch(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Delete
	 */
	public function delete(string $route, $options, $controller = null)
	{
		return Router::initialize()->delete(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Link
	 */
	public function link(string $route, $options, $controller = null)
	{
		return Router::initialize()->link(
			$route,
			...$this->mergeOptions($options, $controller)
		);
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Methods\Unlink
	 */
	public function unlink(string $route, $options, $controller = null)
	{
		return Router::initialize()->unlink(
			$route,
			...$this->mergeOptions($options, $controller)
		);
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
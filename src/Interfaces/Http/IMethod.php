<?php
namespace Shuttle\Interfaces\Http;

use Shuttle\Exceptions\InvalidController;
use Shuttle\Helpers\Str;
use Shuttle\Http\Response;

abstract class IMethod
{
	/** @var string $prefix */
	protected $prefix;

	/** @var string $namespace */
	protected $namespace;

	/** @var string $route */
	protected $route;

	/** @var string[] $prefix_segments */
	protected $prefix_segments = [];

	/** @var string[] $segments */
	protected $segments = [];

	/** @var \Closure|string */
	protected $controller;

	/** @var string[] $middleware */
	protected $middleware = [];

	/** @var \Closure[]|string[] $transformers */
	protected $transformers = [];

	/**
	 * @param string $route
	 * @param array  $options
	 * @param string $controller
	 *
	 * @return void
	 */
	public function __construct(string $route, $options = [], string $controller = null)
	{
		$this->route = Str::startsWith('/', $route)
			? $route : '/' . $route;

		$this->segments  = array_filter(explode('/', $route));
		$this->namespace = app()->getNamespace('controllers');

		if (is_array($options)) {
			$this->parseOptions($options);

			if (!is_string($controller) && !is_callable($controller)) {
				$controller = function() {
					return Response::error(null, 418);
				};
			}
		}

		if (is_string($options) || is_callable($options)) {
			$controller = $options;
		}

		$this->controller = $controller;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return strtoupper(
			basename(
				str_replace(
					'\\', '/', get_class($this)
				)
			)
		);
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 *
	 * @return $this
	 */
	public function prefix(string $prefix)
	{
		$prefix = Str::startsWith('/', $prefix)
			? $prefix : '/' . $prefix;

		$prefix = !Str::endsWith('/', $prefix)
			? $prefix : substr($prefix, 0, -1);

		$this->prefix          = $prefix;
		$this->prefix_segments = array_filter(explode('/', $prefix));

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearPrefix()
	{
		$this->prefix          = null;
		$this->prefix_segments = [];

		return $this;
	}

	/**
	 * @return string
	 */
	public function getRoute()
	{
		return $this->route;
	}

	/**
	 * @return string
	 */
	public function getFullRoute()
	{
		$full_route = $this->prefix . $this->route;

		return Str::endsWith('/', $full_route)
			? substr($full_route, 0, -1) : $full_route;
	}

	/**
	 * @param \Closure|string $controller
	 *
	 * @throws InvalidController
	 * @return $this
	 */
	public function controller($controller)
	{
		if (!is_string($controller) && !is_callable($controller)) {
			throw new InvalidController;
		}

		$this->controller = $controller;

		return $this;
	}

	/**
	 * @return \Closure|string
	 */
	public function getController()
	{
		return $this->controller;
	}

	/**
	 * @return string[]
	 */
	public function getSegments()
	{
		return $this->segments;
	}

	/**
	 * @return string[]
	 */
	public function getPrefixSegments()
	{
		return $this->prefix_segments;
	}

	/**
	 * @return string
	 */
	public function getNamespace()
	{
		return $this->namespace;
	}

	/**
	 * @param string $namespace
	 *
	 * @return $this
	 */
	public function namespace(string $namespace)
	{
		$this->namespace = $namespace;

		return $this;
	}

	/**
	 * @return $this
	 */
	public function clearNamespace()
	{
		$this->namespace = app()->getNamespace('controllers');

		return $this;
	}

	/**
	 * @return \Closure[]|string[]
	 */
	public function getMiddleware()
	{
		return $this->middleware;
	}

	/**
	 * @param string|\Closure|array $middleware
	 *
	 * @return $this
	 */
	public function middleware($middleware)
	{
		$middleware = is_array($middleware)
			? $middleware : func_get_args();

		$this->middleware = $middleware;

		return $this;
	}

	/**
	 * @param string|\Closure|array $middleware
	 *
	 * @return $this
	 */
	public function appendMiddleware($middleware)
	{
		$middleware = is_array($middleware)
			? $middleware : func_get_args();

		$this->middleware = array_merge(
			$this->middleware, $middleware
		);

		return $this;
	}

	/**
	 * @param string|\Closure|array $middleware
	 *
	 * @return $this
	 */
	public function prependMiddleware($middleware)
	{
		$middleware = is_array($middleware)
			? $middleware : func_get_args();

		$this->middleware = array_merge(
			$middleware, $this->middleware
		);

		return $this;
	}

	/**
	 * @return \Closure[]|string[]
	 */
	public function getTransformers()
	{
		return $this->transformers;
	}

	/**
	 * @param array $options
	 *
	 * @return void
	 */
	protected function parseOptions(array $options)
	{
		if (isset($options[ 'prefix' ])) {
			$prefix = $options[ 'prefix' ];

			if (is_string($prefix)) {
				$this->prefix($prefix);
			}
		}

		if (isset($options[ 'middleware' ])) {
			$middleware = $options[ 'middleware' ];

			$this->middleware($middleware);
		}

		if (isset($options[ 'transformers' ])) {
			$transformers = $options[ 'transformers' ];

			if (is_array($transformers)) {
				$this->transformers = array_merge($this->transformers, $transformers);
			}

			if (is_string($transformers)) {
				$this->transformers[] = $transformers;
			}
		}
	}
}
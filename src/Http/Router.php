<?php
namespace Shuttle\Http;

use Shuttle\Application;

use Shuttle\Http\Methods\Get;
use Shuttle\Http\Methods\Post;
use Shuttle\Http\Methods\Put;
use Shuttle\Http\Methods\Patch;
use Shuttle\Http\Methods\Delete;
use Shuttle\Http\Methods\Link;
use Shuttle\Http\Methods\Unlink;

use Shuttle\Interfaces\Http\IMethod;

class Router
{
	/** @var Router $instance */
	private static $instance;

	/** @var Application $app */
	private $app;

	/** @var IMethod[] */
	protected $routes = [];

	/** @var string[] $middleware */
	protected $middleware = [];

	/**
	 * @param Application $app
	 *
	 * @return Router
	 */
	public static function initialize(Application &$app = null)
	{
		if (is_null(static::$instance)) {
			static::$instance = new Router($app);
		}

		return static::$instance;
	}

	/**
	 * @param Application $app
	 *
	 * @return void
	 */
	private function __construct(Application $app)
	{
		$this->app = $app;
	}

	/**
	 * @return void
	 */
	public function capture()
	{
		$request = new Request;

		$query         = $request->uri();
		$method        = $request->method();
		$segment_count = $request->segmentCount();

		$routes = $this->routes;

		foreach ($routes as $route) {

			// Check method
			if ($method !== $route->getMethod()) {
				continue;
			}

			$route_segment_count = (
				count($route->getSegments()) +
				count($route->getPrefixSegments())
			);

			// Check segments
			if ($segment_count !== $route_segment_count) {
				continue;
			}

			// Compare query and route
			$full    = $route->getFullRoute();
			$regexp  = preg_replace('/(:(\w+))/', '(?P<$2>\w+)', $full);
			$compare = preg_match('/' . addcslashes($regexp, '/') . '/', $query, $matches);

			if (empty($compare)) {
				continue;
			}

			$middleware = $route->getMiddleware();

			if (!empty($middleware)) {

				// Check middleware
				foreach ($middleware as $callable) {
					if (!$this->hasMiddleware($callable)) {
						continue;
					}

					$test = $this->callMiddleware($this->getMiddleware($callable), [ $request ]);

					if ($test === 1) {

						/** @var string $callable */
						Response::error(
							'Unable to call ' . $callable . ' middleware'
						)->send();
					}

					if (is_null($test) || is_bool($test) && $test == true) {
						continue;
					}

					if (!($test instanceof Response)) {
						$test = new Response;
					}

					$test->send();
				}
			}

			$controller = $route->getController();

			// Call controller
			$response = $this->callController($controller, [ $request ]);

			if ($response === 1) {
				Response::error(
					'Unable to call ' . (is_callable($controller) ? 'controller' : $controller) . ' middleware'
				)->send();
			}

			if (!($response instanceof Response)) {
				$response = new Response;
			}

			$transformers = $route->getTransformers();

			if (!empty($transformers)) {

				// Transform response
				$last_response = $response;

				foreach ($transformers as $callable) {
					$transformed = $this->callTransformer($callable, [ $last_response, $request ]);

					if ($transformed === 1) {
						Response::error(
							'Unable to call ' . (is_callable($callable) ? 'transformer' : $callable) . ' middleware'
						)->send();
					}

					if (!($transformed instanceof Response)) {
						$transformed = (new Response)->data($transformed);
					}

					$last_response = $transformed;
				}

				$response = $last_response;
			}

			$response->send();
		}

		Response::error('Not Implemented', 501)->send();
	}

	/**
	 * @param string|\Closure $class
	 * @param array           $args
	 *
	 * @return mixed
	 */
	public function callMiddleware($class, array $args = [])
	{
		return $this->callCallable($class, 'middleware', $args);
	}

	/**
	 * @param string|\Closure $class
	 * @param array           $args
	 *
	 * @return mixed
	 */
	public function callController($class, array $args = [])
	{
		return $this->callCallable($class, 'controller', $args);
	}

	/**
	 * @param string|\Closure $class
	 * @param array           $args
	 *
	 * @return mixed
	 */
	public function callTransformer($class, array $args = [])
	{
		return $this->callCallable($class, 'transformer', $args);
	}

	/**
	 * @param string|\Closure $class
	 * @param array           $args
	 * @param string          $type
	 *
	 * @return mixed
	 */
	private function callCallable($class, string $type, array $args = [])
	{
		$type = $type !== 'middleware'
			? "{$type}s" : $type;

		$callable = is_string($class)
			? preg_split('/::|@/', $class) : $class;

		if (is_array($callable)) {
			$class = path($type) . '/' . $callable[ 0 ] . '.php';

			if (!file_exists($class)) {
				return 1;
			}

			/** @noinspection PhpIncludeInspection */
			require_once $class;

			$callable[ 0 ] = app()->getNamespace($type) . $callable[ 0 ];

			if (!class_exists($callable[ 0 ])) {
				return 1;
			}

			if (!method_exists(...$callable)) {
				return 1;
			}
		}

		/** @var array $callable */
		return call_user_func($callable, ...$args);
	}

	/**
	 * @return IMethod[]
	 */
	public function getRoutes()
	{
		return $this->routes;
	}

	/**
	 * @param string $name
	 *
	 * @return string[]|string
	 */
	public function getMiddleware(string $name = null)
	{
		if (is_string($name)) {
			return $this->middleware[ $name ];
		}

		return $this->middleware;
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasMiddleware(string $name)
	{
		return array_key_exists($name, $this->middleware);
	}

	/**
	 * @param string $name
	 * @param string $callable
	 *
	 * @return $this
	 */
	public function registerMiddleware(string $name, string $callable)
	{
		$this->middleware[ $name ] = $callable;

		return $this;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Get
	 */
	public function get(string $route, $options, $controller = null)
	{
		$method = new Get($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Post
	 */
	public function post(string $route, $options, $controller = null)
	{
		$method = new Post($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Put
	 */
	public function put(string $route, $options, $controller = null)
	{
		$method = new Put($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Patch
	 */
	public function Patch(string $route, $options, $controller = null)
	{
		$method = new Patch($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Delete
	 */
	public function delete(string $route, $options, $controller = null)
	{
		$method = new Delete($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Link
	 */
	public function link(string $route, $options, $controller = null)
	{
		$method = new Link($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}

	/**
	 * @param string                $route
	 * @param string|\Closure|array $options
	 * @param string|\Closure       $controller
	 *
	 * @return Unlink
	 */
	public function unlink(string $route, $options, $controller = null)
	{
		$method = new Unlink($route, $options, $controller);

		$this->routes[] = &$method;

		return $method;
	}
}
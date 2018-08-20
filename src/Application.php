<?php
namespace Shuttle;

use Shuttle\Exceptions\DuplicateProvider;
use Shuttle\Http\Router;
use Shuttle\Interfaces\IProvider;

class Application
{
	/** @var Application $instance */
	private static $instance;

	/** @var string[] $paths */
	protected $paths = [];

	/** @var string[] $namespaces */
	protected $namespaces = [];

	/** @var IProvider[] $providers */
	protected $providers = [];

	/** @var Router $router */
	protected $router;

	/**
	 * @param string $base_path
	 * @param string $app_path
	 *
	 * @throws \Exception
	 * @return Application
	 */
	public static function initialize(string $base_path = null, string $app_path = null)
	{
		if (is_null(static::$instance)) {
			if (is_null($base_path)) {
				throw new \Exception(
					'Base path is required for a new instance of Shuttle'
				);
			}

			if (is_null($app_path)) {
				$app_path = $base_path . '/app';
			}

			static::$instance = new Application(
				$base_path, $app_path
			);
		}

		return static::$instance;
	}

	/**
	 * @param string $base_path
	 * @param string $app_path
	 *
	 * @return void
	 */
	private function __construct(string $base_path, string $app_path)
	{
		$this->paths[ 'base' ] = $base_path;
		$this->paths[ 'app' ]  = $app_path;

		$this->paths[ 'providers' ] = $app_path . '/Providers';

		$this->paths[ 'controllers' ]  = $app_path . '/Http/Controllers';
		$this->paths[ 'middleware' ]   = $app_path . '/Http/Middleware';
		$this->paths[ 'transformers' ] = $app_path . '/Http/Transformers';

		$this->setNamespace('controllers', '\\App\\Http\\Controllers\\');
		$this->setNamespace('transformers', '\\App\\Http\\Transformers\\');
		$this->setNamespace('middleware', '\\App\\Http\\Middleware\\');

		$this->setNamespace('providers', 'App\\Providers\\');

		$this->router = Router::initialize($this);
	}

	/**
	 * @return void
	 */
	public function capture()
	{
		$this->router->capture();
	}

	/**
	 * @return string[]
	 */
	public function getPaths()
	{
		return $this->paths;
	}

	/**
	 * @param string $name
	 * @param string $fallback
	 *
	 * @return string|null
	 */
	public function getPath(string $name, string $fallback = null)
	{
		$path = $this->paths[ $name ];

		if (empty($path) && !empty($fallback)) {
			$path = $this->paths[ $name ] = $fallback;
		}

		return $path;
	}

	/**
	 * Clear all paths except `base` and `app`.
	 *
	 * @return $this
	 */
	public function clearPaths()
	{
		$this->paths = array_filter($this->paths, function($key) {
			return in_array($key, [ 'base', 'app' ]);
		}, ARRAY_FILTER_USE_KEY);

		return $this;
	}

	/**
	 * @return string
	 */
	public function getBasePath()
	{
		return $this->getPath('base');
	}

	/**
	 * @param string $base_path
	 *
	 * @return $this
	 */
	public function setBasePath(string $base_path)
	{
		$this->paths[ 'base' ] = $base_path;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAppPath()
	{
		return $this->getPath('app');
	}

	/**
	 * @param string $app_path
	 *
	 * @return $this
	 */
	public function setAppPath(string $app_path)
	{
		$this->paths[ 'app' ] = $app_path;

		return $this;
	}

	/**
	 * @return string|null
	 */
	public function getProviderPath()
	{
		return $this->getPath('providers');
	}

	/**
	 * @param string $providers_path
	 *
	 * @return $this
	 */
	public function setProviderPath(string $providers_path)
	{
		$this->paths[ 'providers' ] = $providers_path;

		return $this;
	}

	/**
	 * @return string[]
	 */
	public function getProviderNames()
	{
		return array_keys($this->providers);
	}

	/**
	 * @param string $name
	 *
	 * @return bool
	 */
	public function hasProvider(string $name)
	{
		return array_key_exists($name, $this->providers);
	}

	/**
	 * @return $this
	 */
	public function clearProviders()
	{
		$this->providers = [];

		return $this;
	}

	/**
	 * @param string $name
	 *
	 * @return IProvider|null
	 */
	public function getProvider(string $name)
	{
		return $this->providers[ $name ];
	}

	/**
	 * @param string $name
	 *
	 * @return $this
	 */
	public function removeProvider(string $name)
	{
		if ($this->hasProvider($name)) {
			unset($this->providers[ $name ]);
		}

		return $this;
	}

	/**
	 * @throws DuplicateProvider
	 * @return $this
	 */
	public function loadProviders()
	{
		$path = $this->getPath('providers');

		$namespace = $this->getNamespace('providers');
		$providers = $this->getFiles($path, 'php');

		foreach ($providers as $provider) {
			$class = $namespace . basename($provider, '.php');

			if (!file_exists($provider)) {
				continue;
			}

			/** @noinspection PhpIncludeInspection */
			require_once $provider;

			if (!class_exists($class)) {
				continue;
			}

			$instance = new $class($this);

			if (!($instance instanceof IProvider)) {
				continue;
			}

			$name = $instance->getReference();

			if ($this->hasProvider($name)) {
				throw new DuplicateProvider($name . ' has already been registered');
			}

			$instance->load();

			$this->providers[ $name ] = &$instance;
		}

		return $this;
	}

	/**
	 * @return Router
	 */
	public function getRouter()
	{
		return $this->router;
	}

	/**
	 * @param string $name
	 * @param string $namespace
	 *
	 * @return $this
	 */
	public function setNamespace(string $name, string $namespace)
	{
		$this->namespaces[ $name ] = $namespace;

		return $this;
	}

	/**
	 * @param string $name
	 * @param string $fallback
	 *
	 * @return string|null
	 */
	public function getNamespace(string $name, string $fallback = null)
	{
		$namespace = $this->namespaces[ $name ];

		if (empty($namespace) && !empty($fallback)) {
			$namespace = $this->namespaces[ $name ] = $fallback;
		}

		return $namespace;
	}

	/**
	 * @param string $path
	 * @param string $extension
	 *
	 * @return array
	 */
	protected function getFiles(string $path, string $extension = null)
	{
		if (empty($path) || !is_dir($path)) {
			return [];
		}

		return empty($extension)
			? glob($path . '/*')
			: glob($path . '/*.' . $extension);
	}
}
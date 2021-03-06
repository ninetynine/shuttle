<?php
if (!function_exists('app')) {
	/**
	 * Get an instance of the application
	 *
	 * @return \Shuttle\Application|null
	 */
	function app()
	{
		try {
			return \Shuttle\Application::initialize();
		} catch (Exception $e) {
			return null;
		}
	}
}

if (!function_exists('provider')) {
	/**
	 * @param string $name
	 *
	 * @return \Shuttle\Interfaces\IProvider|null
	 */
	function provider(string $name)
	{
		return app()->getProvider($name);
	}
}

if (!function_exists('path')) {
	/**
	 * @param string $name
	 *
	 * @return string|null
	 */
	function path(string $name)
	{
		return app()->getPath($name);
	}
}

if (!function_exists('app_path')) {
	/**
	 * @return string|null
	 */
	function app_path()
	{
		return path('app');
	}
}

if (!function_exists('base_path')) {
	/**
	 * @return string|null
	 */
	function base_path()
	{
		return path('base');
	}
}

if (!function_exists('env')) {
	/**
	 * @param string $key
	 * @param mixed  $fallback
	 *
	 * @return mixed
	 */
	function env(string $key, $fallback = null)
	{
		return getenv($key) ?: $fallback;
	}
}

if (!function_exists('dump')) {
	/**
	 * @param mixed $data
	 */
	function dump($data)
	{
		header('Content-Type: text/html');
		$data = func_get_args();

		echo '<pre>';
		var_dump(...$data);
		echo '</pre>';
	}
}

if (!function_exists('dd')) {
	/**
	 * @param mixed $data
	 */
	function dd($data)
	{
		header('Content-Type: text/html');
		$data = func_get_args();

		echo '<pre>';
		var_dump(...$data);
		exit('</pre>');
	}
}


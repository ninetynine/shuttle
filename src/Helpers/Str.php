<?php
namespace Shuttle\Helpers;

class Str
{
	/**
	 * Credit: https://stackoverflow.com/a/834355
	 *
	 * @param string $needle
	 * @param string $haystack
	 *
	 * @return bool
	 */
	public static function startsWith(string $needle, string $haystack)
	{
		$length = strlen($needle);

		return (substr($haystack, 0, $length) === $needle);
	}

	/**
	 * Credit: https://stackoverflow.com/a/834355
	 *
	 * @param string $needle
	 * @param string $haystack
	 *
	 * @return bool
	 */
	public static function endsWith(string $needle, string $haystack)
	{
		$length = strlen($needle);

		if ($length == 0) {
			return true;
		}

		return (substr($haystack, -$length) === $needle);
	}
}
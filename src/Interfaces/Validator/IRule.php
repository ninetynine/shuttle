<?php
namespace Shuttle\Helpers\Validator;

abstract class IRule
{
	/**
	 * @param mixed $data
	 *
	 * @return bool
	 */
	abstract public function validate($data);

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	abstract public function errorMessage(string $key);
}
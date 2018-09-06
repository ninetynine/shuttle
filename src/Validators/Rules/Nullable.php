<?php
namespace Shuttle\Validators\Rules;

use Shuttle\Helpers\Validator\IRule;

class Nullable extends IRule
{
	/**
	 * @param mixed $data
	 *
	 * @return bool
	 */
	public function validate($data)
	{
		return true;
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	public function errorMessage(string $key)
	{
		return '';
	}
}
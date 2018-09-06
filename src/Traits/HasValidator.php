<?php
namespace Shuttle\Traits;

use Shuttle\Validators\Validator;

trait HasValidator
{
	/** @var Validator $validator */
	protected $validator;

	/**
	 * @return array
	 */
	public function errors()
	{
		return $this->validator->errors();
	}

	/**
	 * @return bool
	 */
	public function hasErrors()
	{
		return $this->validator->hasErrors();
	}
}
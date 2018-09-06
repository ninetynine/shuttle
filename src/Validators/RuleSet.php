<?php
namespace Shuttle\Validators;

class RuleSet
{
	/** @var array $rules */
	protected $rules;

	/**
	 * @param array $rules
	 */
	public function __construct(array $rules = [])
	{
		$this->rules = $rules;
	}

	/**
	 * @return array
	 */
	public function getRules()
	{
		return $this->rules;
	}

	/**
	 * @param string $key
	 * @param mixed  $rules
	 *
	 * @return $this
	 */
	public function addRule(string $key, $rules)
	{
		$this->rules[ $key ] = $rules;

		return $this;
	}
}
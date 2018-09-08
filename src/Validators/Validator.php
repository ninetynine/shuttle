<?php
namespace Shuttle\Validators;

use Shuttle\Interfaces\Validators\IRule;
use Shuttle\Exceptions\InvalidRule;

class Validator
{
	/** @var array $data */
	protected $data;

	/** @var array $rule_set */
	protected $rule_set;

	/** @var array $errors */
	protected $errors = [];

	/**
	 * @param array $data
	 * @param array $rule_set
	 */
	public function __construct(array $data = [], array $rule_set = [])
	{
		$this->data     = $data;
		$this->rule_set = $rule_set;
	}

	/**
	 * @param array $data
	 *
	 * @return $this
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * @param array $rule_set
	 *
	 * @return $this
	 */
	public function setRuleSet(array $rule_set)
	{
		$this->rule_set = $rule_set;

		return $this;
	}

	/**
	 * @return array
	 */
	public function errors()
	{
		return $this->errors;
	}

	/**
	 * @return bool
	 */
	public function hasErrors()
	{
		return !empty($this->errors);
	}

	/**
	 * @param array $errors
	 *
	 * @return $this
	 */
	public function setErrors(array $errors)
	{
		$this->errors = $errors;

		return $this;
	}

	/**
	 * @param string $key
	 * @param array  $errors
	 *
	 * @return $this
	 */
	public function addErrors(string $key, array $errors)
	{
		$this->errors[ $key ] = $errors;

		return $this;
	}

	/**
	 * @param string $key
	 * @param string $error
	 *
	 * @return $this
	 */
	public function addError(string $key, string $error)
	{
		if (!is_array($this->errors[ $key ])) {
			$this->errors[ $key ] = [];
		}

		$this->errors[ $key ][] = $error;

		return $this;
	}

	/**
	 * @param array $data
	 * @param array $rule_set
	 *
	 * @throws InvalidRule
	 * @return bool
	 */
	public function validate(array $data = [], array $rule_set = [])
	{
		if (empty($data)) {
			$data = $this->data;
		}

		if (empty($rule_set)) {
			$rule_set = $this->rule_set;
		}

		foreach ($rule_set as $key => $rules) {
			$this->validateRule($rules, $data[ $key ] ?? null, $key);
		}

		return !$this->hasErrors();
	}

	/**
	 * @param mixed  $rules
	 * @param mixed  $data
	 * @param string $key
	 *
	 * @throws InvalidRule
	 */
	private function validateRule($rules, $data, $key)
	{
		if (!is_array($rules)) {
			$rule_set = $this->instantiateRuleSet($rules);

			$rules = !is_null($rule_set)
				? $rule_set->getRules() : [ $rules ];
		}

		foreach ($rules as $rule) {
			$rule = $this->instantiateRule($rule);

			if (!$rule->validate($data)) {
				$this->addError($key, $rule->errorMessage($key));
			}
		}
	}

	/**
	 * @param mixed $rule
	 *
	 * @throws InvalidRule
	 * @return IRule
	 */
	private function instantiateRule($rule)
	{
		if (is_string($rule) && class_exists($rule)) {
			$rule = new $rule;
		}

		if (!($rule instanceof IRule)) {
			throw new InvalidRule;
		}

		return $rule;
	}

	/**
	 * @param mixed $rule_set
	 *
	 * @return RuleSet|null
	 */
	private function instantiateRuleSet($rule_set)
	{
		if (is_string($rule_set) && class_exists($rule_set)) {
			$rule_set = new $rule_set;
		}

		if (!($rule_set instanceof RuleSet)) {
			return null;
		}

		return $rule_set;
	}
}
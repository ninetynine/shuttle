<?php
namespace Shuttle\Exceptions;

class Exception extends \Exception
{
	/** @var mixed $data */
	protected $data;

	/**
	 * @param mixed $data
	 *
	 * @return $this
	 */
	public function setData($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}
}
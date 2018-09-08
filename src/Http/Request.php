<?php
namespace Shuttle\Http;

use Shuttle\Helpers\Str;
use Shuttle\Traits\HasValidator;
use Shuttle\Validators\Validator;

use Shuttle\Exceptions\InvalidData;

class Request
{
	use HasValidator;

	/** @var string QUERY_STRING */
	const QUERY_STRING = '__SHUTTLE__';

	/** @var string $uri */
	protected $uri;

	/** @var string $method */
	protected $method;

	/** @var string[] $params */
	protected $params = [];

	/** @var mixed[] $body */
	protected $body = [];

	/** @var string[] $segments */
	protected $segments = [];

	public function __construct()
	{
		$query = $_GET[ self::QUERY_STRING ];

		$this->uri      = Str::startsWith('/', $query) ? $query : '/' . $query;
		$this->segments = array_filter(explode('/', $this->uri));
		$this->method   = strtoupper($_SERVER[ 'REQUEST_METHOD' ]);

		$this->params = array_filter($_GET, function($param) {
			return $param !== self::QUERY_STRING;
		}, ARRAY_FILTER_USE_KEY);

		$body = $_POST;

		if (empty($_POST)) {
			$body = file_get_contents("php://input");
			$body = json_decode($body, true);
		}

		$this->body      = $body;
		$this->validator = new Validator;
	}

	/**
	 * @return string
	 */
	public function uri()
	{
		return $this->uri;
	}

	/**
	 * @return string[]
	 */
	public function segments()
	{
		return $this->segments;
	}

	/**
	 * @return int
	 */
	public function segmentCount()
	{
		return count($this->segments);
	}

	/**
	 * @return string
	 */
	public function method()
	{
		return $this->method;
	}

	/**
	 * @return string[]
	 */
	public function params()
	{
		return $this->params;
	}

	/**
	 * @return mixed[]
	 */
	public function body()
	{
		return $this->body;
	}

	/**
	 * @return mixed[]
	 */
	public function all()
	{
		return array_merge($this->params, $this->body);
	}

	/**
	 * @param string|string[] $input
	 *
	 * @return array
	 */
	public function only($input)
	{
		$inputs = is_array($input)
			? $input : func_get_args();

		return array_filter($this->all(), function($input) use ($inputs) {
			return in_array($input, $inputs);
		}, ARRAY_FILTER_USE_KEY);
	}

	/**
	 * @param array $rules
	 *
	 * @throws \Shuttle\Exceptions\InvalidRule
	 * @return Validator
	 */
	public function validate(array $rules)
	{
		$this->validator->validate($this->all(), $rules);

		return $this->validator;
	}

	/**
	 * @param array $rules
	 *
	 * @throws \Shuttle\Exceptions\InvalidRule
	 * @throws \Shuttle\Exceptions\InvalidData
	 * @return Validator
	 */
	public function volatileValidate(array $rules)
	{
		$this->validate($rules);

		if ($this->hasErrors()) {
			throw (new InvalidData)->setData($this->errors());
		}

		return $this->validator;
	}
}
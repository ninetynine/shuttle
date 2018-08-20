<?php
namespace Shuttle\Http;

class Response
{
	/** @var int $status */
	protected $status;

	/** @var bool $success */
	protected $success;

	/** @var string|array|null $error */
	protected $error;

	/** @var string|array|null $data */
	protected $data;

	/** @var array|null $meta */
	protected $meta;

	/**
	 * @param string|array $data
	 * @param array        $meta
	 * @param int          $status
	 *
	 * @return Response
	 */
	public static function success($data = null, array $meta = null, int $status = 200)
	{
		$instance = new Response;

		$instance->status  = $status;
		$instance->success = true;
		$instance->data    = $data;
		$instance->meta    = $meta;

		return $instance;
	}

	/**
	 * @param string|array $error
	 * @param int          $status
	 *
	 * @return Response
	 */
	public static function error($error = null, int $status = 500)
	{
		$instance = new Response;

		$instance->status  = $status;
		$instance->success = false;
		$instance->error   = $error;

		return $instance;
	}

	/**
	 * @param int $status
	 *
	 * @return $this
	 */
	public function status(int $status)
	{
		$this->status = $status;

		return $this;
	}

	/**
	 * @param string|array $data
	 *
	 * @return $this
	 */
	public function data($data = null)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * @param array $meta
	 *
	 * @return $this
	 */
	public function meta(array $meta = null)
	{
		$this->meta = $meta;

		return $this;
	}

	/**
	 * @return void
	 */
	public function send()
	{
		header('Content-Type: application/json');
		http_response_code($this->status);

		exit(json_encode(get_object_vars($this)));
	}
}
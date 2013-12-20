<?php namespace Leeb\Jsonrpc\Exceptions;

abstract class JsonrpcException extends \Exception
{
	protected $message;
	protected $jsonrpc_error_code;
	protected $data;

	public function __construct($data = null)
	{
		parent::__construct($this->message);
		$this->data = $data;
	}

	public function getJsonrpcErrorCode()
	{
		return $this->jsonrpc_error_code;
	}

	public function getData()
	{
		return $this->data;
	}
}
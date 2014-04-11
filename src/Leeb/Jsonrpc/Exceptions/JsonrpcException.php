<?php namespace Leeb\Jsonrpc\Exceptions;

abstract class JsonrpcException extends \Exception
{
	protected $data;

	public function __construct($message, $code, $data = null)
	{
		parent::__construct($this->message, $this->code);
		$this->data = $data;
	}

	public function getData()
	{
		return $this->data;
	}
}
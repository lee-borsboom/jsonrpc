<?php namespace Leeb\Jsonrpc;

class JsonrpcError
{
	public $code;
	public $message;
	public $data;

	public function __construct($code, $message, $data)
	{
		$this->setCode($code);
		$this->setMessage($message);
		$this->setData($data);
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function setData($data)
	{
		if ($data === null) {
			unset($this->data);
		} else {
			$this->data = $data;
		}
	}
}
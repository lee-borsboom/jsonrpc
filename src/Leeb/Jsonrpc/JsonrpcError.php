<?php namespace Leeb\Jsonrpc;

class JsonrpcError
{
	public $id;
	public $code;
	public $message;
	public $error;

	public function __construct($id, $code, $message, $error)
	{
		$this->setId($id);
		$this->setCode($code);
		$this->setMessage($message);
		$this->setError($error);
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setCode($code)
	{
		$this->code = $code;
	}

	public function setMessage($message)
	{
		$this->message = $message;
	}

	public function setError($error)
	{
		if ($error === null) {
			unset($this->error);
		} else {
			$this->error = $error;
		}
	}
}
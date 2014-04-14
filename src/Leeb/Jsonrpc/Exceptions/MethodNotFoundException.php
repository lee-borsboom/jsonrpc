<?php namespace Leeb\Jsonrpc\Exceptions;

class MethodNotFoundException extends JsonrpcException
{
	protected $message = 'Method not found';
	protected $code = -32601;
	protected $data;

	public function __construct($data = null)
	{
		parent::__construct($data, $this->code);
	}
}
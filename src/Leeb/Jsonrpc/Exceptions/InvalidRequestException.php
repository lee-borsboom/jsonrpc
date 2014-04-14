<?php namespace Leeb\Jsonrpc\Exceptions;

class InvalidRequestException extends JsonrpcException
{
	protected $message = 'Invalid request';
	protected $code = -32600;
	protected $data;

	public function __construct($data = null)
	{
		parent::__construct($data, $this->code);
	}
}
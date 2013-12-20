<?php namespace Leeb\Jsonrpc\Exceptions;

class InternalErrorException extends JsonrpcException
{
	protected $message = 'Internal error';
	protected $jsonrpc_error_code = -32603;
	protected $data;

	public function __construct($data = null)
	{
		parent::__construct($data);
	}
}
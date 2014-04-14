<?php namespace Leeb\Jsonrpc\Exceptions;

class ParseErrorException extends JsonrpcException
{
	protected $message = 'Parse Error';
	protected $code = -32700;
	protected $data;

	public function __construct($data = null)
	{
		parent::__construct($data, $this->code);
	}
}
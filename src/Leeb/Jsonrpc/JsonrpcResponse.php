<?php namespace Leeb\Jsonrpc;

class JsonrpcResponse
{
	public $id;
	public $jsonrpc = '2.0';

	public function __construct($id, $body_property, $body)
	{
		$this->$body_property = $body;
		$this->id = $id;
	}
}
<?php namespace Leeb\Jsonrpc\Interfaces;

interface RawRequestInterpreterInterface
{
	public function interpretRawRequest(array $raw_request);
}
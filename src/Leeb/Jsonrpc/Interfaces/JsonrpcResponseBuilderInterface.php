<?php namespace Leeb\Jsonrpc\Interfaces;

interface JsonrpcResponseBuilderInterface
{
	public function buildFromResult($request, $result);
	public function buildFromException($request, \Exception $exception);
}
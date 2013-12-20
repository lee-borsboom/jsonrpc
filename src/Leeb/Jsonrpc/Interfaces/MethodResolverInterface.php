<?php namespace Leeb\Jsonrpc\Interfaces;

interface MethodResolverInterface
{
	public function resolve($client_method_string);
}
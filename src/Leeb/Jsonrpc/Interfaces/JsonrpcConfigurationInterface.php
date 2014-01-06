<?php namespace Leeb\Jsonrpc\Interfaces;

interface JsonrpcConfigurationInterface
{
	public function getRoutePrefix();
	public function getResolutionPattern();
	public function getResolver();
}
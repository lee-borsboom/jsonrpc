<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\JsonrpcConfigurationInterface;

class JsonrpcConfiguration implements JsonrpcConfigurationInterface
{
	const DEFAULT_RESOLUTION_PATTERN = '\\{class}\\{{class}}Controller';

	public function getResolutionPattern()
	{
		return \Config::get('jsonrpc::resolution_pattern', self::DEFAULT_RESOLUTION_PATTERN);
	}

	public function getRoutePrefix()
	{
		return \Config::get('jsonrpc::route_prefix', '');
	}
}
<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\MethodResolverInterface;
use Leeb\Jsonrpc\Interfaces\JsonrpcConfigurationInterface;
use Leeb\Jsonrpc\Exceptions\MethodNotFoundException;
use Leeb\Jsonrpc\Exceptions\InternalErrorException;

class MethodResolver implements MethodResolverInterface
{
	public function __construct(JsonrpcConfigurationInterface $configuration)
	{
		$this->configuration = $configuration;
	}

	public function resolve($client_method_string)
	{
		$controller_name = $this->getControllerName($client_method_string);

		try {
			$controller = $this->loadController($controller_name);
		} catch (\ReflectionException $e) {
			$this->handleReflectionException($e, $controller_name, $client_method_string);
		}

		$client_method_pieces = $this->splitClientMethodString($client_method_string);
		$method_name = $this->extractMethodName($client_method_pieces);

		if ( ! \method_exists($controller, $method_name)) {
			throw new MethodNotFoundException($client_method_string);
		}

		return array($controller, $method_name);
	}

	protected function handleReflectionException(\ReflectionException $e, $controller_name,
		$client_method_string
	) {
		if (strstr($e->getMessage(), 'Class '.$controller_name.' does not exist')) {
			throw new MethodNotFoundException($client_method_string);
		} else {
			throw new InternalErrorException('Unknown error occurred');
		}
	}

	protected function getControllerName($client_method_string)
	{
		$custom_resolver = $this->configuration->getResolver();

		if (is_callable($custom_resolver)) {
			return call_user_func($custom_resolver, $client_method_string);
		}

		return $this->resolveUsingDefaultMethod($client_method_string);
	}

	protected function resolveUsingDefaultMethod($client_method_string)
	{
		$client_method_pieces = $this->splitClientMethodString($client_method_string);

		$resolution_pattern = $this->configuration->getResolutionPattern();
		return $this->extractControllerName($client_method_pieces, $resolution_pattern);
	}
	
	protected function loadController($controller_name)
	{
		return \App::make($controller_name);
	}

	protected function extractControllerName($client_method_pieces, $pattern)
	{
		$controller_name = $client_method_pieces[0];

		return str_replace('{class}', $controller_name, $pattern);
	}

	protected function extractMethodName($client_method_pieces)
	{
		return end($client_method_pieces);
	}

	protected function splitClientMethodString($client_method_string)
	{
		return explode('.', $client_method_string);
	}
}
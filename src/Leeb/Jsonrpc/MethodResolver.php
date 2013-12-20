<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\MethodResolverInterface;
use Leeb\Jsonrpc\Exceptions\MethodNotFoundException;

class MethodResolver implements MethodResolverInterface
{
	public function resolve($client_method_string)
	{
		$client_method_pieces = $this->splitClientMethodString($client_method_string);
		$controller_name = $this->extractControllerName($client_method_pieces);

		try {
			$controller = $this->loadController($controller_name);
		} catch (\Exception $e) {
			throw new MethodNotFoundException($client_method_string);
		}

		$method_name = $this->extractMethodName($client_method_pieces);

		if ( ! \method_exists($controller, $method_name)) {
			throw new MethodNotFoundException($client_method_string);
		}

		return array($controller, $method_name);
	}
	
	protected function loadController($namespaced_controller_name)
	{
		return new $namespaced_controller_name();
	}

	protected function extractControllerName($client_method_pieces)
	{
		$pattern = \Config::get('jsonrpc::resolution_pattern');
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
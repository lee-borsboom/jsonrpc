<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RoutableInterface;
use Leeb\Jsonrpc\Interfaces\MethodResolverInterface;
use Leeb\Jsonrpc\Interfaces\RequestInterface;

class RoutableNotification implements RoutableInterface
{
	protected $resolver;
	protected $request;

	public function __construct(MethodResolverInterface $resolver, RequestInterface $request)
	{
		$this->setResolver($resolver);
		$this->setRequest($request);
	}

	public function route()
	{
		try {
			$callable = $this->resolver->resolve($this->request->getMethod());
			$this->executeRequest($callable, $this->request);
		} catch (\Exception $e) { }

		return null;
	}

	public function setResolver(MethodResolverInterface $resolver)
	{
		$this->resolver = $resolver;
	}

	public function setRequest(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function isNotification() {
		return true;
	}

	protected function executeRequest(array $callable, RequestInterface $request)
	{
		return call_user_func($callable, $request);
	}
}
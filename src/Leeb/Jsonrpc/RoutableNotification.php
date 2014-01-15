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
			\Request::replace($this->request->rawData());
			$this->executeRequest($callable);
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

	protected function executeRequest(array $callable)
	{
		\Event::fire('jsonrpc.beforeExecution', array($callable[0], $callable[1]));
		return call_user_func($callable);
	}
}
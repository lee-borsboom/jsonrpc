<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RoutableInterface;
use Leeb\Jsonrpc\Interfaces\MethodResolverInterface;
use Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface;
use Leeb\Jsonrpc\Interfaces\RequestInterface;

class RoutableRequest implements RoutableInterface
{
	protected $resolver;
	protected $request;

	public function __construct(MethodResolverInterface $resolver, JsonrpcResponseBuilderInterface $response_builder,
		RequestInterface $request)
	{
		$this->setResolver($resolver);
		$this->setRequest($request);
		$this->setResponseBuilder($response_builder);
	}

	public function route()
	{
		try {
			$callable = $this->resolver->resolve($this->request->getMethod());
			$result = $this->executeRequest($callable, $this->request);
			$response = $this->response_builder->buildFromResult($this->request, $result);
			\Event::fire('jsonrpc.beforeOutput', array($response, $callable[0], $callable[1]));
			return $response;
		} catch (\Exception $e) {
			return $this->response_builder->buildFromException($this->request, $e);
		}
	}

	public function setResolver(MethodResolverInterface $resolver)
	{
		$this->resolver = $resolver;
	}

	public function setRequest(RequestInterface $request)
	{
		$this->request = $request;
	}

	public function setResponseBuilder(JsonrpcResponseBuilderInterface $response_builder)
	{
		$this->response_builder = $response_builder;
	}

	public function isNotification()
	{
		return false;
	}

	protected function executeRequest(array $callable, RequestInterface $request)
	{
		\Event::fire('jsonrpc.beforeExecution', array($request, $callable[0], $callable[1]));
		return call_user_func($callable, $request);
	}
}
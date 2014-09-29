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
			\Request::replace($this->request->rawData());
			$callable = $this->resolver->resolve($this->request->getMethod());
			$result = $this->executeRequest($callable);
			$response = $this->response_builder->buildFromResult($this->request, $result);
			$this->fireBeforeOutputEvent($response, $callable);
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

	protected function executeRequest(array $callable)
	{
        $params = $this->request->rawData();
		$this->fireBeforeExecutionEvent($callable, $params);
		return call_user_func_array($callable, $params);
	}

	protected function fireBeforeExecutionEvent($callable, $params)
	{
		\Event::fire('jsonrpc.beforeExecution', array($callable[0], $callable[1], $params));
	}

	protected function fireBeforeOutputEvent($response, $callable)
	{
		\Event::fire('jsonrpc.beforeOutput', array($response, $callable[0], $callable[1]));
	}
}
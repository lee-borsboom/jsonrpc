<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface;
use Leeb\Jsonrpc\Interfaces\JsonrpcConfigurationInterface;
use Leeb\Jsonrpc\Exceptions\JsonrpcException;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;

class JsonrpcResponseBuilder implements JsonrpcResponseBuilderInterface
{
	const SUCCESS_PROPERTY = 'result';
	const ERROR_PROPERTY = 'error';

	public function __construct(JsonrpcConfigurationInterface $config_service)
	{
		$this->config_service = $config_service;
	}

	public function buildFromResult($request, $result)
	{
		if ($result instanceof JsonResponse) {
			$raw_result = json_decode($result->getContent());
		} else if ($result instanceof Response) {
			$raw_result = $result->getOriginalContent();
		} else {
			$raw_result = $result;
		}

		return $this->buildRaw($request, self::SUCCESS_PROPERTY, $raw_result);
	}

	public function buildFromException($request, \Exception $exception)
	{
		$handler = $this->config_service->getExceptionHandler();

		if ($handler) {
			return $handler($request, $exception);
		}

		if ($exception instanceOf JsonrpcException) {
			return $this->buildFromJsonrpcException($request, $exception);
		}

		return $this->buildFromGenericException($request, $exception);
	}

	private function buildFromGenericException($request, $exception)
	{
		$args = array(
			$request ? $request->getId() : null,
			-32603,
			'Internal Error',
			$exception->getMessage()
		);

		$body = \App::make('Leeb\Jsonrpc\JsonrpcError', $args);
		return $this->buildRaw($request, self::ERROR_PROPERTY, $body);
	}

	private function buildFromJsonrpcException($request, JsonrpcException $exception)
	{
		$args = array(
			$request ? $request->getId() : null,
			$exception->getCode(),
			$exception->getMessage(),
			method_exists($exception, 'getError') ? $exception->getError() : ''
		);

		$body = \App::make('Leeb\Jsonrpc\JsonrpcError', $args);
		return $this->buildRaw($request, self::ERROR_PROPERTY, $body);
	}

	private function buildRaw($request, $output_field, $output_body)
	{
		$id = null;

		if ($request !== null) {

			if ($request->isNotification()) {
				return null;
			}

			$id = $request->getId();
		}
		
		return \App::make('Leeb\Jsonrpc\JsonrpcResponse', array($id, $output_field, $output_body));
	}
}
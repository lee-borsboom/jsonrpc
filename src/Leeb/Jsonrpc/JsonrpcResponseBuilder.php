<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface;
use Leeb\Jsonrpc\Exceptions\JsonrpcException;

class JsonrpcResponseBuilder implements JsonrpcResponseBuilderInterface
{
	const SUCCESS_PROPERTY = 'result';
	const ERROR_PROPERTY = 'error';

	public function buildFromResult($request, $result)
	{
		return $this->buildRaw($request, self::SUCCESS_PROPERTY, $result);
	}

	public function buildFromException($request, \Exception $exception)
	{
		if ($exception instanceOf JsonrpcException) {
			return $this->buildFromJsonrpcException($request, $exception);
		}

		return $this->buildFromGenericException($request, $exception);
	}

	private function buildFromGenericException($request, $exception)
	{
		$args = array(
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
			$exception->getJsonrpcErrorCode(),
			$exception->getMessage(),
			$exception->getData()
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
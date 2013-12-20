<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RawRequestInterpreterInterface;
use Leeb\Jsonrpc\Interfaces\RequestValidatorInterface;
use Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface;
use Leeb\Jsonrpc\Exceptions\InvalidRequestException;

class RawRequestInterpreter implements RawRequestInterpreterInterface
{
	public function __construct(RequestValidatorInterface $request_validator,
		JsonrpcResponseBuilderInterface $response_builder)
	{

		$this->setRequestValidator($request_validator);
		$this->setResponseBuilder($response_builder);
	}

	public function interpretRawRequest(array $raw_request)
	{
		if ($this->isBatchRequest($raw_request)) {
			return $this->interpretBatchRequest($raw_request);
		}

		return $this->interpretSingleRequest($raw_request);
	}

	public function interpretSingleRequest(array $raw_single_request)
	{
		return $this->buildSingleRequest($raw_single_request);
	}

	public function interpretBatchRequest(array $raw_batch_request)
	{
		$batch = \App::make('Leeb\Jsonrpc\RoutableBatch', array(null));

		foreach ($raw_batch_request as $raw_request) {
			try {
				$request = $this->buildSingleRequest($raw_request);
				$batch->addIndividualRequest($request);
			} catch (\Exception $e) {
				$response = $this->response_builder->buildFromException(null, $e);
				$batch->addIndividualResponse($response);
			}
		}

		return $batch;
	}

	public function isBatchRequest($raw_request)
	{
		if ( ! is_array($raw_request)) {
			throw new InvalidRequestException();
		}

		if (isset($raw_request['jsonrpc'])) {
			return false;
		}

		if (isset($raw_request[0]) && isset($raw_request[0]['jsonrpc'])) {
			return true;
		}

		throw new InvalidRequestException();
	}

	public function setRequestValidator(RequestValidatorInterface $request_validator)
	{
		$this->request_validator = $request_validator;
	}

	public function setResponseBuilder(JsonrpcResponseBuilderInterface $response_builder)
	{
		$this->response_builder = $response_builder;
	}

	protected function buildSingleRequest($raw_single_request)
	{
		$this->request_validator->validate($raw_single_request);
		$request = \App::make('Leeb\Jsonrpc\Interfaces\RequestInterface', array($raw_single_request));

		if ( ! $request->isNotification()) {
			$routable = \App::make('Leeb\Jsonrpc\RoutableRequest', array($request));
		} else {
			$routable = \App::make('Leeb\Jsonrpc\RoutableNotification', array($request));
		}
		return $routable;
	}
}
<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RouterInterface;
use Leeb\Jsonrpc\Interfaces\RawRequestInterpreterInterface;
use Leeb\Jsonrpc\Interfaces\JsonrpcResponseBuilderInterface;
use Leeb\Jsonrpc\Exceptions\ParseErrorException;

class Router implements RouterInterface
{
	public function __construct(RawRequestInterpreterInterface $interpreter,
		JsonrpcResponseBuilderInterface $response_builder)
	{
		$this->interpreter = $interpreter;
		$this->response_builder = $response_builder;
	}

	public function route()
	{
		try {
			$raw_request = $this->getRawRequest();
			$routable = $this->interpreter->interpretRawRequest($raw_request);
			$result = $routable->route();
		} catch (\Exception $e) {
			$result = $this->response_builder->buildFromException(null, $e);
		}

		$this->outputResult($result);
	}

	protected function getRawRequest()
	{
		$json = \Input::json()->all();

		if (sizeof($json) == 0) {
			throw new ParseErrorException();
		}

		return $json;
	}

	protected function outputResult($result)
	{
		if (is_array($result) || is_object($result)) {
			echo json_encode($result);
		} else {
			echo $result;
		}
	}
}
<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RequestValidatorInterface;
use Leeb\Jsonrpc\Exceptions\InternalErrorException;

class RequestValidator implements RequestValidatorInterface
{
	public function validate(array $request)
	{
		if ( ! $this->isValidRequest($request)) {
			throw new InternalErrorException();
		}
	}

	public function isValidRequest(array $request)
	{
		return $this->hasValidVersion($request) &&
			$this->hasValidMethodFormat($request) &&
			$this->hasValidParameters($request);
	}

	public function hasValidVersion(array $request)
	{
		return isset($request['jsonrpc']) && $request['jsonrpc'] === '2.0';
	}

	public function hasValidMethodFormat(array $request)
	{
		$regex = '/^[A-Z][[:alpha:]]+(\.[[:alpha:]]+)+$/';
		return isset($request['method']) && \preg_match($regex, $request['method']);
	}

	public function hasValidParameters(array $request)
	{
		return ! array_key_exists('params', $request) ||
			is_array($request['params']) ||
			is_object($request['params']) ||
			$request['params'] === null;
	}
}
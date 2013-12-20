<?php namespace Leeb\Jsonrpc\Interfaces;

interface RequestValidatorInterface
{
	public function validate(array $request);
	public function isValidRequest(array $request);
	public function hasValidVersion(array $request);
	public function hasValidMethodFormat(array $request);
	public function hasValidParameters(array $request);
}
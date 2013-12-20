<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RequestValidatorInterface;
use Leeb\Jsonrpc\Interfaces\RequestInterface;

class Request implements RequestInterface
{
	protected $id;
	protected $version;
	protected $method;
	protected $params;

	public function __construct($request_data)
	{
		$this->setMethod($request_data['method']);
		$this->setVersion($request_data['jsonrpc']);

		if (array_key_exists('id', $request_data)) {
			$this->setId($request_data['id']);
		}

		if (array_key_exists('params', $request_data)) {
			$this->setParams($request_data['params']);
		}
	}

	public function data($property_name)
	{
		if ( ! isset($this->params[$property_name])) {
			return null;
		}

		return $this->params[$property_name];
	}

	public function rawData()
	{
		return $this->params;
	}

	public function getId()
	{
		return $this->id;
	}

	public function getVersion()
	{
		return $this->version;
	}

	public function getMethod()
	{
		return $this->method;
	}

	public function isNotification()
	{
		return $this->id === null;
	}

	public function setId($id)
	{
		$this->id = $id;
	}

	public function setMethod($method)
	{
		$this->method = $method;
	}

	public function setParams($params)
	{
		if ($params === null) {
			$this->params = array();
		} else if (is_object($params)) {
			$this->params = (array)$params;
		} else {
			$this->params = $params;
		}
	}

	public function setVersion($version)
	{
		$this->version = $version;
	}
}
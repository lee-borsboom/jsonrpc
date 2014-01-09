<?php namespace Leeb\Jsonrpc\Interfaces;

interface RequestInterface
{
	public function __construct($raw_data);
	public function data($property_name);
	public function rawData();
	public function isNotification();
	public function getId();
	public function getVersion();
	public function getMethod();
	public function setId($id);
	public function setMethod($method);
	public function setVersion($version);
	public function setParams($params);
}
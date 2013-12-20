<?php namespace Leeb\Jsonrpc;

use Leeb\Jsonrpc\Interfaces\RoutableInterface;

class RoutableBatch implements RoutableInterface
{
	protected $individual_requests;
	protected $responses = array();
	
	public function __construct(array $individual_requests = null)
	{
		if ($individual_requests !== null) {
			$this->setIndividualRequests($individual_requests);
		}
	}

	public function route()
	{
		$output = $this->responses;

		foreach ($this->individual_requests as $request) {
			
			$result = $request->route();

			if ( ! $request->isNotification()) {
				$output[] = $result;
			}
		}

		return $output;
	}

	public function setIndividualRequests(array $individual_requests)
	{
		foreach ($individual_requests as $individual_request) {
			$this->addIndividualRequest($individual_request);
		}
	}

	public function addIndividualRequest(RoutableInterface $individual_request)
	{
		$this->individual_requests[] = $individual_request;
	}

	public function addIndividualResponse(JsonrpcResponse $response)
	{
		if ($response !== null) {
			$this->responses[] = $response;
		}
	}
}
<?php

class HttpClient
{
	public $status;

	public $response;

	public function get($url)
	{
		$this->status = NULL;
		$this->response = @file_get_contents($url);

		if (isset($http_response_header))
		{
			$this->parseResponseHeader($http_response_header);
			return TRUE;
		}
		else
		{
			$this->response = NULL;
			return FALSE;
		}
	}

	private function parseResponseHeader($header)
	{
		// HTTP/1.1 200 OK
		$pattern = '#HTTP/\d\.\d (\d{3})#';

		// Clear fields
		$this->status = NULL;

		foreach($header as $entry)
		{
			$match = preg_match($pattern, $entry, $matches);

			if ($match == 1)
			{
				$this->status = (int)$matches[1];
				break;
			}
		}
	}
}
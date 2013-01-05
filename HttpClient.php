<?php

class HttpClient
{
	public $status;
	public $contentType;

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
		$this->parseResponseHeaderEntry('#^HTTP/\d\.\d (\d{3})#', $this->status, $header);

		// Content-Type: text/html; charset=UTF-8
		$this->parseResponseHeaderEntry('/^Content-Type: (.+)$/', $this->contentType, $header);
	}

	private function parseResponseHeaderEntry($pattern, &$destination, $header)
	{
		$destination = NULL;

		foreach($header as $entry)
		{
			$match = preg_match($pattern, $entry, $matches);

			if ($match == 1)
			{
				$destination = $matches[1];
				return TRUE;
			}
		}

		return FALSE;
	}
}
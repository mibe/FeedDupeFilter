<?php

/**
 * A simple HTTP client.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class HttpClient
{
	/**
	 * The HTTP status code of the last request.
	 */
	public $status;

	/**
	 * The 'Content-Type' header of the last request.
	 */
	public $contentType;

	/**
	 * The response of the last request.
	 *
	 * @var string
	 */
	public $response;

	/**
	 * Issues an GET request to the specified URL.
	 *
	 * Throws an exception if the URL is empty.
	 *
	 * @param string $url
	 * @return void
	 */
	public function get($url)
	{
		if (empty($url) || !is_string($url))
			throw new InvalidArgumentException('Invalid URL given. Must be a non-empty string.');

		$this->status = NULL;
		$this->response = @file_get_contents($url);

		// If the request fails, the $http_response_header wouldn't exist.
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

	/**
	 * Parses the needed entries from the response header.
	 *
	 * Needs the contents of the predefined $http_response_header variable.
	 *
	 * @param array
	 * @return void
	 */
	private function parseResponseHeader($header)
	{
		// HTTP/1.1 200 OK
		$this->parseResponseHeaderEntry('#^HTTP/\d\.\d (\d{3})#', $this->status, $header);

		// Content-Type: text/html; charset=UTF-8
		$this->parseResponseHeaderEntry('/^Content-Type: (.+)$/', $this->contentType, $header);

		// Cast the status code to integer. The regex has secured that this is an integer.
		if ($this->status != NULL)
			$this->status = (int)$this->status;
	}

	/**
	 * Uses an regular expression to search for the correct value in the header array.
	 *
	 * @param string $pattern Regular expression with one group
	 * @param string &$destination Variable to save the value in (call by reference)
	 * @param array $header
	 * @return bool TRUE if pattern matched and group content saved in destination
	 */
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
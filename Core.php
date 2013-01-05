<?php

require('ArchiveBase.php');
require('FileArchive.php');
require('HttpClient.php');

class Core
{
	private $feedUrl;
	private $archive;
	private $http;

	function __construct($feedUrl)
	{
		$this->feedUrl = $feedUrl;

		$this->archive = new FileArchive($feedUrl);
		$this->http = new HttpClient();
	}

	public function Filter()
	{
		$result = $this->http->get($this->feedUrl);

		if ($result === FALSE || $this->http->status != 200)
			$this->handleHttpErrors();
	}

	private function handleHttpErrors()
	{
		header('HTTP/1.1 500 Server Error');
		exit(sprintf('Error retrieving feed URL "%s"', $this->feedUrl));
	}
}
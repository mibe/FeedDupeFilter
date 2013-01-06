<?php

require('ArchiveBase.php');
require('FileArchive.php');

require('FeedItemBase.php');
require('Rss2FeedItem.php');
require('FeedManipulatorBase.php');
require('Rss2FeedManipulator.php');

require('HttpClient.php');

class Core
{
	private $feedUrl;
	private $archive;
	private $http;
	private $feedManipulator;

	function __construct($feedUrl)
	{
		$this->feedUrl = $feedUrl;

		$this->archive = new FileArchive($feedUrl);
		$this->http = new HttpClient();

		$this->fetchFeed();
		$this->detectManipulator();
	}

	private function detectManipulator()
	{
		$rss2 = new Rss2FeedManipulator($this->http->response);

		if ($rss2->isSupported())
			$this->feedManipulator = $rss2;
	}

	private function fetchFeed()
	{
		// Retrieve Feed
		$result = $this->http->get($this->feedUrl);

		if ($result === FALSE || $this->http->status != 200)
			$this->handleHttpErrors();
	}

	public function Filter()
	{
		$this->feedManipulator->parseFeed();
	}

	private function handleHttpErrors()
	{
		header('HTTP/1.1 500 Server Error');
		$msg = sprintf('Error retrieving feed URL "%s" (HTTP Status: %d)', $this->feedUrl, $this->http->status);
		exit($msg);
	}
}
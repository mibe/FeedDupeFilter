<?php

require('ArchiveBase.php');
require('FileArchive.php');

require('FeedItemBase.php');
require('Rss2FeedItem.php');
require('AtomFeedItem.php');
require('Rss1FeedItem.php');
require('FeedManipulatorBase.php');
require('Rss2FeedManipulator.php');
require('AtomFeedManipulator.php');
require('Rss1FeedManipulator.php');

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
		$atom = new AtomFeedManipulator($this->http->response);
		$rss1 = new Rss1FeedManipulator($this->http->response);

		if ($rss2->isSupported())
			$this->feedManipulator = $rss2;
		else if ($atom->isSupported())
			$this->feedManipulator = $atom;
		else if ($rss1->isSupported())
			$this->feedManipulator = $rss1;
		else
			die ("WTF?");
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
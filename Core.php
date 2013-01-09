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

		// Use the feed URL as identifier
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

	public function filter()
	{
		$this->feedManipulator->parseFeed();

		foreach($this->feedManipulator as $item)
		{
			$uid = $this->buildUniqueId($item);

			// Check if the item was already seen.
			// If yes, remove it. If no, add it to the archive.
			if ($this->archive->contains($uid))
				$this->feedManipulator->removeItem($item);
			else
				$this->archive->add($uid);
		}

		// Filtering is done, now build and output the altered feed.
		// Also use the same Content-Type of the feed, so the encoding won't get lost.
		header('Content-Type: ' . $this->http->contentType);
		print $this->feedManipulator->buildFeed();
	}

	private function handleHttpErrors()
	{
		header('HTTP/1.1 500 Server Error');
		$msg = sprintf('Error retrieving feed URL "%s" (HTTP Status: %d).', $this->feedUrl, $this->http->status);
		exit($msg);
	}

	private function buildUniqueId(FeedItemBase $feedItem)
	{
		return sha1($feedItem->title);
	}
}
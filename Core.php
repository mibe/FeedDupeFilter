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

	public static $manipulatorClasses = array('Rss2', 'Atom', 'Rss1');

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
		foreach(self::$manipulatorClasses as $class)
		{
			$className = $class . 'FeedManipulator';
			$instance = new $className($this->http->response);

			// If this manipulator supports the feed, use it and end the probing.
			if ($instance->isSupported())
			{
				$this->feedManipulator = $instance;
				return;
			}
		}

		self::generateHttpError(501, 'Unsupported feed: No feed manipulator for this type of feed found.');
	}

	private function fetchFeed()
	{
		// Retrieve Feed
		$result = $this->http->get($this->feedUrl);

		if ($result === FALSE || $this->http->status != 200)
		{
			$msg = sprintf('Error retrieving feed URL "%s" (HTTP Status: %d).', $this->feedUrl, $this->http->status);
			self::generateHttpError(500, $msg);
		}
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

	private function buildUniqueId(FeedItemBase $feedItem)
	{
		return sha1($feedItem->title);
	}

	public static function generateHttpError($errorCode, $errorMessage)
	{
		switch($errorCode)
		{
			case 404: $errorCode .= ' File Not Found';
				break;
			case 500: $errorCode .= ' Server Error';
				break;
			case 501: $errorCode .= ' Not Implemented';
				break;
		}

		header('HTTP/1.1 ' . $errorCode);
		exit($errorMessage);
	}
}
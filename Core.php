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
		if (empty($feedUrl))
			throw new InvalidArgumentException('Empty string given.');

		$this->feedUrl = $feedUrl;

		// Use the feed URL as identifier
		$this->archive = new FileArchive($feedUrl);
		$this->http = new HttpClient();

		$this->fetchFeed();
		$this->detectManipulator();
	}

	private function detectManipulator()
	{
		$lastLoadingError = '';

		foreach(self::$manipulatorClasses as $class)
		{
			$className = $class . 'FeedManipulator';
			$instance = new $className();
			$loaded = $instance->loadFeed($this->http->response);

			// If the feed couldn't get loaded properly, save the error message
			// and try another manipulator.
			if (!$loaded)
			{
				$lastLoadingError = $instance->loadingError;
				continue;
			}

			// If this manipulator supports the feed, use it and end the probing.
			if ($instance->isSupported())
			{
				$this->feedManipulator = $instance;
				return;
			}
		}

		$msg = 'Unsupported feed: No feed manipulator for this type of feed found.';

		if (!empty($lastLoadingError))
			$msg .= sprintf(' Last error message of the XML parser was: %s', $lastLoadingError);

		self::generateHttpError(501, $msg);
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

	public static function generateHttpError($errorCode, $errorMessage = '')
	{
		if (!is_numeric($errorCode) || $errorCode < 100 || $errorCode > 599)
			throw new InvalidArgumentException('errorCode is not an valid HTTP status code.');

		switch($errorCode)
		{
			case 400: $errorCode .= ' Bad Request';
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
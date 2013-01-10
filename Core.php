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

/**
 * Main class for filtering duplicated entries in RSS / ATOM feeds.
 *
 * The feed is loaded from the remote server and the XML parsed by PHP's
 * DOMDocument class. Then every feed entry is checked against an archive
 * to detect, if the feed entry was already seen. If this is true, the entry
 * would be removed from the feed. After every entry is checked, the possibly
 * altered feed XML is build again and sent to the client.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class Core
{
	private $feedUrl;
	private $archive;
	private $http;
	private $feedManipulator;

	/**
	 * List of feed manipulators which should be probed and used to
	 * manipulate the feed.
	 *
	 * @static
	 * @var array
	 */
	public static $manipulatorClasses = array('Rss2', 'Atom', 'Rss1');

	/**
	 * Constructor of the class. Needs the URL of the feed.
	 *
	 * @param string $feedUrl URL to the feed which should be filtered.
	 */
	function __construct($feedUrl)
	{
		if (empty($feedUrl) || !is_string($feedUrl))
			throw new InvalidArgumentException('Invalid feed URL given. Must be a non-empty string.');

		$this->feedUrl = $feedUrl;

		// Use the feed URL as identifier
		$this->archive = new FileArchive($feedUrl);
		$this->http = new HttpClient();

		// Download the feed and check if a manipulator supports it
		$this->fetchFeed();
		$this->detectManipulator();
	}

	/**
	 * Tries to detect the appropriate feed manipulator which can handle the 
	 * downloaded feed, depending on the type of the feed.
	 *
	 * Every class name in the {@link $manipulatorClasses} array will be
	 * instantiated and probed if it supports the feed type.
	 *
	 * If the XML of the feed could not be parsed properly an exception with
	 * more information about the cause will be thrown.
	 *
	 * @return void
	 */
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

		// Impossible to work without a manipulator.
		throw new ErrorException($msg, 501);
	}

	/**
	 * Retrieve the feed from the remote server.
	 *
	 * If the feed could not be downloaded an exception will be thrown with
	 * information about the cause.
	 *
	 * @return void
	 */
	private function fetchFeed()
	{
		// Retrieve Feed
		$result = $this->http->get($this->feedUrl);

		if ($result === FALSE || $this->http->status != 200)
		{
			$msg = sprintf('Error retrieving feed URL "%s" (HTTP Status: %d).', $this->feedUrl, $this->http->status);
			throw new ErrorException($msg, 500);
		}
		else if (empty($this->http->response))
		{
			$msg = sprintf('The retrieved feed was empty. (HTTP Status: %d).', $this->http->status);
			throw new ErrorException($msg, 500);
		}
	}

	/**
	 * Filter out duplicated items in the feed. The resulting feed is directly sent to the client.
	 *
	 * The same Content-Type header field as the original feed had is also set so
	 * the MIME type (and encoding) the remote server used isn't lost.
	 *
	 * @return void
	 */
	public function filter()
	{
		// Parse the feed and extract all items.
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
		// Also use the same Content-Type of the feed, so the MIME type
		// (and encoding) won't get lost.
		header('Content-Type: ' . $this->http->contentType);
		print $this->feedManipulator->buildFeed();
	}

	/**
	 * Generate an unique identifier from the feed item.
	 *
	 * @param FeedItemBase $feedItem 
	 * @return string
	 */
	private function buildUniqueId(FeedItemBase $feedItem)
	{
		return sha1($feedItem->title);
	}
}
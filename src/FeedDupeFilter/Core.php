<?php
namespace FeedDupeFilter;

use FeedDupeFilter\Archive\FileArchive;
use FeedDupeFilter\Feed\FeedItemBase;

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
	/**
	 * URL of the original feed.
	 *
	 * @var string
	 */
	private $feedUrl;

	/**
	 * Instance of the used archive.
	 *
	 * @see ArchiveBase
	 * @var ArchiveBase
	 */
	private $archive;

	/**
	 * Instance of the HTTP client.
	 *
	 * @var HttpClient
	 */
	private $http;

	/**
	 * Instance of the manipulator used to alter the feed.
	 *
	 * @var FeedManipulatorBase
	 */
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
			throw new \InvalidArgumentException('Invalid feed URL given. Must be a non-empty string.');

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
	 * Every class name in the $manipulatorClasses array will be
	 * instantiated and probed if it supports the feed type.
	 *
	 * If the XML of the feed could not be parsed properly an exception with
	 * more information about the cause will be thrown.
	 *
	 * @return void
	 * @see $manipulatorClasses
	 */
	private function detectManipulator()
	{
		$lastLoadingError = '';

		foreach(self::$manipulatorClasses as $class)
		{
			$className = 'FeedDupeFilter\\Feed\\' . $class . 'FeedManipulator';
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
		throw new \ErrorException($msg, 501);
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
			throw new \ErrorException($msg, 500);
		}
		else if (empty($this->http->response))
		{
			$msg = sprintf('The retrieved feed was empty. (HTTP Status: %d).', $this->http->status);
			throw new \ErrorException($msg, 500);
		}
	}

	/**
	 * Filter out duplicated items in the feed.
	 *
	 * If the feed is sent to the client, the same Content-Type header field
	 * as the original feed had is set so the MIME type (and encoding)
	 * the remote server used isn't lost.
	 *
	 * If the feed is not sent to the client, the resulting array contains the
	 * feed in the 'output' element and the header fields in the 'header' element.
	 *
	 * @param bool $directOutput TRUE if the feed is sent to the client.
	 * @return void|array
	 */
	public function filter($directOutput = TRUE)
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
		$output = $this->feedManipulator->buildFeed();

		// Also use the same Content-Type of the feed, so the MIME type
		// (and encoding) won't get lost.
		$header = array('Content-Type' => $this->http->contentType);

		if ($directOutput)
		{
			foreach($header as $key => $value)
				header(sprintf('%s: %s', $key, $value));

			exit($output);
		}
		else
			return array('output' => $output, 'header' => $header);
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
<?php

/**
 * Represents a class for manipulating XML feeds.
 *
 * This class is abstract; the methods {@link isSupported()} and {@link parseFeed()}
 * must be implemented in subclasses.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
abstract class FeedManipulatorBase implements IteratorAggregate, Countable
{
	/**
	 * The DOM structure of the feed.
	 *
	 * @var DOMDocument
	 */
	protected $feed;

	/**
	 * The entries of the feed.
	 *
	 * @var array
	 */
	protected $items;

	/**
	 * Contains the last error message, if errors occurred during loading of the feed.
	 *
	 * If no errors occurred, FALSE is returned.
	 *
	 * @var false|string
	 */
	public $loadingError;

	/**
	 * Constructor of the class.
	 */
	function __construct()
	{
		$this->items = array();
		$this->loadingError = FALSE;
	}

	/**
	 * Error handler when loading the feed XML.
	 *
	 * This handler is set and unset in {@link loadFeed()} to catch errors
	 * which occurred during parsing of the XML document in DOMDocument.
	 * The error message is extracted from the error string by using an
	 * regular expression and stored in the {@link loadingError} field.
	 *
	 * @return false|void
	 */
	public function xmlErrorHandler($code, $message)
	{
		if ($code != E_WARNING)
			return FALSE;

		// DOMDocument::loadXML(): Entity 'raquo' not defined in Entity, line: 69;
		// DOMDocument::loadXML() [domdocument.loadxml]: Entity 'raquo' not defined in Entity, line: 69
		$pattern = '/^DOMDocument::loadXML\(\)(:| \[.+\]:) (.+)$/';
		$match = '';

		// If this regex matches, the message is in the 3rd array element (2nd group).
		if (preg_match($pattern, $message, $match) === 1)
			$this->loadingError = $match[2];
		else
			return FALSE;
	}

	/**
	 * Parses the raw XML feed.
	 *
	 * Throws an exception if the feed is empty or not a string.
	 *
	 * @param string
	 * @return bool
	 */
	public function loadFeed($rawFeed)
	{
		if (empty($rawFeed) || !is_string($rawFeed))
			throw new InvalidArgumentException('Invalid feed given. Must be a non-empty string.');

		set_error_handler(array(&$this, 'xmlErrorHandler'));

		$this->feed = new DOMDocument();
		$this->feed->preserveWhitespace = FALSE;

		$result = $this->feed->loadXML($rawFeed);

		restore_error_handler();

		return $result;
	}

	/**
	 * Returns the finished XML feed.
	 *
	 * @return string
	 */
	public function buildFeed()
	{
		return $this->feed->saveXML();
	}

	/**
	 * Removes an entry from the feed.
	 *
	 * @param FeedItemBase Item to remove
	 * @return void
	 */
	public function removeItem(FeedItemBase $item)
	{
		// Remove from feed
		$item->xmlElement->parentNode->removeChild($item->xmlElement);

		// Remove from item array
		if (($key = array_search($item, $this->items, TRUE)) !== FALSE)
			unset($this->items[$key]);
	}

	/**
	 * Returns if this feed manipulator is capable of manipulating the given feed.
	 *
	 * @return bool
	 */
	abstract public function isSupported();

	/**
	 * Parses the feed to extract all feed entries.
	 *
	 * @return void
	 */
	abstract public function parseFeed();

	/**
	 * Returns an Iterator instance for iterating the feed entries.
	 *
	 * Do not call offsetUnset(). Use {@link removeItem()} instead.
	 *
	 * @return ArrayIterator
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	/**
	 * Returns the number of feed entries.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->items);
	}
}
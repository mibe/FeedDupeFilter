<?php

abstract class FeedManipulatorBase implements IteratorAggregate, Countable
{
	protected $feed;
	protected $items;

	public $loadingError;

	function __construct()
	{
		$this->items = array();
		$this->loadingError = FALSE;
	}

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

	public function buildFeed()
	{
		return $this->feed->saveXML();
	}

	public function removeItem(FeedItemBase $item)
	{
		// Remove from feed
		$item->xmlElement->parentNode->removeChild($item->xmlElement);

		// Remove from item array
		if (($key = array_search($item, $this->items, TRUE)) !== FALSE)
			unset($this->items[$key]);
	}

	abstract public function isSupported();
	abstract public function parseFeed();

	public function getIterator()
	{
		return new ArrayIterator($this->items);
	}

	public function count()
	{
		return count($this->items);
	}
}
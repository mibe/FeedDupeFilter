<?php

abstract class FeedManipulatorBase implements IteratorAggregate, Countable
{
	protected $feed;
	protected $items;

	function __construct($rawFeed)
	{
		$this->feed = new DOMDocument();
		$this->feed->loadXML($rawFeed);

		$this->items = array();
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
<?php

abstract class FeedManipulatorBase
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
		return $this->feed->asXML();
	}

	abstract public function isSupported();
	abstract public function removeItem(FeedItem $item);
	abstract public function parseFeed();
}
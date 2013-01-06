<?php

class AtomFeedManipulator extends FeedManipulatorBase
{
	function __construct($rawFeed)
	{
		parent::__construct($rawFeed);
	}

	public function isSupported()
	{
		$feed = $this->feed->getElementsByTagName('feed');

		if ($feed->length == 0)
			return FALSE;

		$xmlns = $feed->item(0)->getAttribute('xmlns');

		return $xmlns == 'http://www.w3.org/2005/Atom';
	}

	public function removeItem(FeedItem $item)
	{
	}

	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('entry');

		foreach($items as $item)
		{
			$fItem = new AtomFeedItem();
			$fItem->parseXml($item);

			$this->items[] = $fItem;
		}
	}
}
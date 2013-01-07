<?php

class Rss2FeedManipulator extends FeedManipulatorBase
{
	function __construct($rawFeed)
	{
		parent::__construct($rawFeed);
	}

	public function isSupported()
	{
		$rss = $this->feed->getElementsByTagName('rss');

		if ($rss->length == 0)
			return FALSE;

		$version = $rss->item(0)->getAttribute('version');

		return $version == '2.0';
	}

	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('item');

		foreach($items as $item)
		{
			$fItem = new Rss2FeedItem();
			$fItem->parseXml($item);

			$this->items[] = $fItem;
		}
	}
}
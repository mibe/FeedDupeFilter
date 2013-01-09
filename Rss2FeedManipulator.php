<?php

class Rss2FeedManipulator extends FeedManipulatorBase
{
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
			$fItem = new Rss2FeedItem($item);
			$fItem->parseXml();

			$this->items[] = $fItem;
		}
	}
}
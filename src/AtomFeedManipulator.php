<?php

/**
 * Represents a class for manipulating XML feeds in ATOM format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class AtomFeedManipulator extends FeedManipulatorBase
{
	/**
	 * {@inheritdoc}
	 */
	public function isSupported()
	{
		$feed = $this->feed->getElementsByTagName('feed');

		if ($feed->length == 0)
			return FALSE;

		$xmlns = $feed->item(0)->getAttribute('xmlns');

		return $xmlns == 'http://www.w3.org/2005/Atom';
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('entry');

		foreach($items as $item)
		{
			$fItem = new AtomFeedItem($item);
			$fItem->parseXml();

			$this->items[] = $fItem;
		}
	}
}
<?php

/**
 * This file is part of the FeedDupeFilter project.
 *
 * @package FeedDupeFilter
 * @link http://github.com/mibe/FeedDupeFilter
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace FeedDupeFilter\Feed;

/**
 * Represents a class for manipulating XML feeds in RSS 2.0 format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class Rss2FeedManipulator extends FeedManipulatorBase
{
	/**
	 * {@inheritdoc}
	 */
	public function isSupported()
	{
		$rss = $this->feed->getElementsByTagName('rss');

		if ($rss->length == 0)
			return FALSE;

		$version = $rss->item(0)->getAttribute('version');

		return $version == '2.0';
	}

	/**
	 * {@inheritdoc}
	 */
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
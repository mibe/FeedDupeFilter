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
 * Represents a class for manipulating XML feeds in RSS 0.91 / 0.92 format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2016 Michael Bemmerl
 */
class Rss09FeedManipulator extends FeedManipulatorBase
{
	/**
	 * {@inheritdoc}
	 */
	public function isSupported()
	{
		$rss = $this->feed->documentElement;
		$version = $rss->getAttribute('version');

		return ($version == '0.92' || $version == '0.91');
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('item');

		foreach($items as $item)
		{
			$fItem = new Rss09FeedItem($item);
			$fItem->parseXml();

			$this->items[] = $fItem;
		}
	}
}
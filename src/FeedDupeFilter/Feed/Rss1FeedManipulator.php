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
 * Represents a class for manipulating XML feeds in RSS 1.0 format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class Rss1FeedManipulator extends FeedManipulatorBase
{
	/**
	 * {@inheritdoc}
	 */
	public function isSupported()
	{
		$rdf = $this->feed->documentElement;

		if ($rdf->tagName != 'rdf:RDF')
			return FALSE;

		return $rdf->isDefaultNamespace('http://purl.org/rss/1.0/');
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseFeed()
	{
		$items = $this->feed->getElementsByTagName('item');

		foreach($items as $item)
		{
			$fItem = new Rss1FeedItem($item);
			$fItem->parseXml();

			$this->items[] = $fItem;
		}
	}
}
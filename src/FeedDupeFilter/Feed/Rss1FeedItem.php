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
 * Represents an feed item in RSS 1.0 / 1.1 format.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class Rss1FeedItem extends FeedItemBase
{
	/**
	 * {@inheritdoc}
	 */
	public function __construct(\DOMElement $xmlElement)
	{
		parent::__construct($xmlElement);
	}

	/**
	 * {@inheritdoc}
	 */
	public function parseXml()
	{
		$this->title = $this->getXmlChildValue('title');
		$this->description = $this->getXmlChildValue('description');
		$this->link = $this->getXmlChildValue('link');
	}
}
<?php
namespace FeedDupeFilter\Identifier;

use FeedDupeFilter\Feed\FeedItemBase;

/**
 * Defines an interface for returning an unique identifier of an feed item.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
interface IIdentifier
{
	/**
	 * Returns an identifier which is unique among the other feed entries
	 * or NULL, if the feed is not compatible to this identifier.
	 *
	 * @param FeedItemBase The feed entry
	 * @return string|null
	 */
	public function getIdentifyingData(FeedItemBase $item);
}
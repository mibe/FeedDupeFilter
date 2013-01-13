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
	 * or NULL, if the feed is not incompatible.
	 *
	 * The feed is incompatible to this identifier, if the feed specification
	 * does not have that element the identifier uses. This is also the case,
	 * if the feed is just not conform to the feed specification.
	 *
	 * @param FeedItemBase The feed entry
	 * @return string|null
	 */
	public function getIdentifyingData(FeedItemBase $item);
}
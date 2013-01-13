<?php
namespace FeedDupeFilter\Identifier;

use FeedDupeFilter\Feed\FeedItemBase;

/**
 * Uses the 'Id' element of an feed item as unique identifier.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class IdIdentifier implements IIdentifier
{
	/**
	 * {@inheritdoc}
	 */
	public function getIdentifyingData(FeedItemBase $item)
	{
		return $item->id;
	}
}
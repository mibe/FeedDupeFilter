<?php

/**
 * This file is part of the FeedDupeFilter project.
 *
 * @package FeedDupeFilter
 * @link http://github.com/mibe/FeedDupeFilter
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace FeedDupeFilter\Identifier;

use FeedDupeFilter\Feed\FeedItemBase;

/**
 * Uses the 'Title' element of an feed item as unique identifier.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class TitleIdentifier implements IIdentifier
{
	/**
	 * {@inheritdoc}
	 */
	public function getIdentifyingData(FeedItemBase $item)
	{
		return $item->title;
	}
}
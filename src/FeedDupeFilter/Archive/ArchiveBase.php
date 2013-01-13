<?php
namespace FeedDupeFilter\Archive;

/**
 * Represents an data archive which contains different items.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
abstract class ArchiveBase implements \Countable
{
	/**
	 * Contents of the archive.
	 *
	 * @var array
	 */
	protected $contents;

	/**
	 * Identifier of the archive.
	 *
	 * @var string
	 */
	protected $archiveIdentifier;

	/**
	 * Constructor of the class. Needs an archive identifier, which is used to
	 * uniquely identify any archive.
	 *
	 * The archive is loaded in the constructor. The loading takes place in the
	 * subclasses, which implement the two abstract methods.
	 *
	 * Throws an exception, if the identifier is empty or not a string.
	 *
	 * @param string Identifier of the archive
	 */
	function __construct($archiveIdentifier)
	{
		if (empty($archiveIdentifier) || !is_string($archiveIdentifier))
			throw new \InvalidArgumentException('Invalid archiveIdentifier given. Must be a non-empty string.');

		$this->archiveIdentifier = $archiveIdentifier;

		$this->clear();
	}

	/**
	 * Loads the archive.
	 *
	 * @return void
	 */
	abstract public function load();

	/**
	 * Saves the archive.
	 *
	 * @return void
	 */
	abstract public function save();

	/**
	 * Add an entry to the archive.
	 *
	 * The entry is checked against duplicates.
	 *
	 * @param string
	 * @return void
	 */
	public function add($uid)
	{
		if (!$this->contains($uid))
			$this->contents[] = $uid;
	}

	/**
	 * Remove an element from the archive.
	 *
	 * @param string
	 * @return void
	 */
	public function remove($uid)
	{
		if (($key = array_search($uid, $this->contents, TRUE)) !== FALSE)
			unset($this->contents[$key]);
	}

	/**
	 * Check if the archive already contains an specified element.
	 *
	 * @param string
	 * @return bool TRUE if the element is already in the archive.
	 */
	public function contains($uid)
	{
		return in_array($uid, $this->contents, TRUE);
	}

	/**
	 * Remove every element in the archive. The archive will be empty after that.
	 *
	 * @return void
	 */
	public function clear()
	{
		$this->contents = array();
	}

	/**
	 * Returns the number of entries in the archive.
	 *
	 * @return int
	 */
	public function count()
	{
		return count($this->contents);
	}
}

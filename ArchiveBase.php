<?php

/**
 * Represents an data archive which contains different items.
 *
 * This class is abstract; the methods {@link load()} and {@link save()} have to be
 * implemented in subclasses.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
abstract class ArchiveBase
{
	protected $contents;
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
			throw new InvalidArgumentException('Invalid archiveIdentifier given. Must be a non-empty string.');

		$this->archiveIdentifier = $archiveIdentifier;

		$this->load();
	}

	/**
	 * Destructor of the class. Saves the archive back to its storage medium.
	 *
	 * The saving is done in the subclass, which implements the abstract
	 * {@link save()} method.
	 */
	function __destruct()
	{
		$this->save();
	}

	/**
	 * Loads the archive.
	 */
	abstract protected function load();

	/**
	 * Saves the archive.
	 */
	abstract protected function save();

	/**
	 * Add an entry to the archive.
	 *
	 * The entry is checked against duplicates.
	 *
	 * @param string
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
	 * @returns bool TRUE if the element is already in the archive.
	 */
	public function contains($uid)
	{
		return in_array($uid, $this->contents, TRUE);
	}

	/**
	 * Remove every element in the archive. The archive will be empty after that.
	 */
	public function clear()
	{
		$this->contents = array();
	}
}

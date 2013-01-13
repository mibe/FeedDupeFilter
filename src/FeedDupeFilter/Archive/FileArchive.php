<?php

/**
 * This file is part of the FeedDupeFilter project.
 *
 * @package FeedDupeFilter
 * @link http://github.com/mibe/FeedDupeFilter
 * @license http://opensource.org/licenses/MIT MIT License
 */

namespace FeedDupeFilter\Archive;

/**
 * Implements an archive which is stored in the local filesystem.
 *
 * The archive identifier is stripped from any non-latin char
 * and used as the file name.
 *
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 */
class FileArchive extends ArchiveBase
{
	/**
	 * Path to the file, which contains the archive.
	 *
	 * Is filled in the buildFilename() method.
	 *
	 * @var string
	 * @see buildFilename()
	 */
	private $file;

	/**
	 * {@inheritdoc}
	 *
	 * @param string Identifier of the archive.
	 * @param string Directory in which the archive files are stored.
	 */
	function __construct($archiveIdentifier, $directory)
	{
		parent::__construct($archiveIdentifier);

		$this->buildFilename($directory);
	}

	/**
	 * Generates the filename from the archive identifier.
	 *
	 * Throws an exception if the given directory is not writeable.
	 *
	 * @param string Directory, in which the archive file is stored.
	 * @return void
	 */
	private function buildFilename($directory)
	{
		// Get the filesystem path.
		$directory = realpath($directory);

		if (!is_writable($directory))
			throw new \InvalidArgumentException('The given directory is not writable.');

		$this->file = $directory . DIRECTORY_SEPARATOR;

		// Replace all characters which are not digits and latin chars with a dash
		$this->file .= preg_replace("/[^a-zA-Z0-9]/", '-', $this->archiveIdentifier);

		// Chop the filename if it's longer than 255 chars.
		// Some filesystems have a max. path length of > 255
		if (strlen($this->file) > 255)
			$this->file = substr($this->file, -255);
	}

	/**
	 * {@inheritdoc}
	 *
	 * If the archive file does not exist, it will be automatically created.
	 *
	 * @return void
	 */
	public function load()
	{
		// Only unserialize if the data file exists.
		// If not, start with an empty archive.
		if (file_exists($this->file))
		{
			$data = file_get_contents($this->file);
			$this->contents = unserialize($data);
		}
		else
		{
			$this->clear();
			$this->save();
		}
	}

	/**
	 * {@inheritdoc}
	 *
	 * Throws an exception if the file could not be written.
	 *
	 * @return void
	 */
	public function save()
	{
		// If nothing has changed, then there's nothing to do here.
		if (!$this->changed)
			return;

		$data = serialize($this->contents);
		$bytes = file_put_contents($this->file, $data);

		if ($bytes === FALSE)
			throw new \ErrorException(sprintf('Could not write archive to file "%s".', $this->file));
	}
}

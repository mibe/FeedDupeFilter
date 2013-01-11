<?php
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
	 */
	function __construct($archiveIdentifier)
	{
		parent::__construct($archiveIdentifier);
	}

	/**
	 * Generates the filename from the archive identifier.
	 *
	 * @return void
	 */
	private function buildFilename()
	{
		// Replace all characters which are not digits and latin chars with a dash
		$this->file = preg_replace("/[^a-zA-Z0-9]/", '-', $this->archiveIdentifier);

		// Chop the filename if it's longer than 200 chars.
		if (strlen($this->file) > 200)
			$this->file = substr($this->file, -200);
	}

	/**
	 * {@inheritdoc}
	 *
	 * If the archive file does not exist, it will be automatically created.
	 *
	 * @return void
	 */
	protected function load()
	{
		if (empty($this->file))
			$this->buildFilename();

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

		// Get realpath, because otherwise with a relative filepath the
		// file would be written to SERVER_ROOT in the save() method (which
		// is called in the parent destructor).
		$this->file = realpath($this->file);
	}

	/**
	 * {@inheritdoc}
	 *
	 * Throws an exception if the file could not be written.
	 *
	 * @return void
	 */
	protected function save()
	{
		if (empty($this->file))
			$this->buildFilename();

		$data = serialize($this->contents);
		$bytes = file_put_contents($this->file, $data);

		if ($bytes === FALSE)
			throw new \Exception(sprintf('Could not write archive to file "%s".', $this->file));
	}
}

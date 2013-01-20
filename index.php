<?php

/**
 * Simple front end for FeedDupeFilter.
 *
 * @package FeedDupeFilter
 * @link http://github.com/mibe/FeedDupeFilter
 * @author Michael Bemmerl <mail@mx-server.de>
 * @copyright Copyright (C) 2013 Michael Bemmerl
 * @license http://opensource.org/licenses/MIT MIT License
 */

/**
 * Sets the desired HTTP status code and stops the interpreter.
 *
 * @param int HTTP status code to be used.
 * @param string Error message.
 * @return void
 */
function fdf_generateHttpError($errorCode, $errorMessage = '')
{
	if (!is_numeric($errorCode) || $errorCode < 100 || $errorCode > 599)
		throw new InvalidArgumentException('errorCode is not an valid HTTP status code.');

	switch($errorCode)
	{
		case 400: $errorCode .= ' Bad Request';
			break;
		case 500: $errorCode .= ' Server Error';
			break;
		case 501: $errorCode .= ' Not Implemented';
			break;
	}

	header('HTTP/1.1 ' . $errorCode);
	exit($errorMessage);
}

/**
 * Autoloader for the script.
 *
 * @param string Name of the class to be loaded, with namespace.
 * @return void
 */
function fdf_autoloader($className)
{
	// Root dir
	$dir = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;

	$fileName  = '';
	$namespace = '';

	if ($lastNsPos = strrpos($className, '\\'))
	{
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}

	// Add relevant parts to filename
	$fileName .= $className . '.php';
	$fileName = $dir . $fileName;

	require $fileName;
}


// Check if the feed URL was specified by the caller.
if (!isset($_GET['feed']))
	fdf_generateHttpError(400, 'No feed URL specified. Supply a valid feed URL in the \'feed\' parameter.');

// Get the feed URL
$feed = $_GET['feed'];

// Register the above autoloader
spl_autoload_register('fdf_autoloader');

try
{
	// Instantiate the Core class and filter the feed.
	// Use LinkIdentifier as class for identifying feed entries.
	$archive = new FeedDupeFilter\Archive\FileArchive($feed, 'archive');
	$identifier = new FeedDupeFilter\Identifier\LinkIdentifier();
	$core = new FeedDupeFilter\Core($feed, $archive, $identifier);
	$newFeed = $core->filter(TRUE);
}
catch (Exception $ex)
{
	$code = $ex->getCode();
	$message = $ex->getMessage();

	if ($code == 0)
		$code = 400;

	fdf_generateHttpError($code, $message);
}

# FeedDupeFilter

FeedDupeFilter is a framework for filtering out duplicated entries in
syndication feeds.

## Requirements

* PHP 5.3
* DOM
* OpenSSL for SSL-encrypted feeds

Tested with PHP 5.3.6.

## Usage
### Single installation

FeedDupeFilter has a frontend for single installations integrated. Just open
index.php in your favorite browser and supply the feed URL to the 'feed'
parameter and FeedDupeFilter will filter out all duplicated entries. After you
checked everything is working, use the URL as new URL to the feed in your
favorite news reader.

The single installation frontend uses the URL of the link element of every feed
entry to determine, if this entry was already seen.

### Example

http://your-host.tld/FeedDupeFilter/?feed=https://github.com/mibe.atom

## Framework installation

FeedDupeFilter can be integrated in existing programs easily. It's in
compliance with PSR-0, so your class loader should be able to load the code.
The root namespace is 'FeedDupeFilter'. This was tested with Symphony's
ClassLoader component and Jonathan Wage's SplClassLoader.

### Example

```php
// The URL to the feed, which should be checked against duplicates.
$feed = 'https://github.com/mibe.atom';

// Use the filesystem as storage medium. Save the files in the "archive"
// directory. Use the feed URL as unique archive identifier.
$archive = new FeedDupeFilter\Archive\FileArchive($feed, 'archive');

// We want to filter out the feed entries based on the entry title.
// So if there is an entry with the same title, filter this entry out!
$identifier = new FeedDupeFilter\Identifier\TitleIdentifier();

// Instantiate the main class. Needs URL to feed, the storage medium and
// the information, which element of the feed entry is decisive.
$core = new FeedDupeFilter\Core($feed, $archive, $identifier);

// Parse the feed and filter every entry out which is a duplicate.
$core->filter();
```

### Documentation

The documentation can be found in the "gh-pages" branch or on GitHub Pages:
http://mibe.github.io/FeedDupeFilter/

## PSR

This project is in compliance with the following standards:
* PSR-0
* PSR-1

## License

This project uses the MIT License for distribution. See the LICENSE.txt file.
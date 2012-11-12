#!/usr/bin/php
<?php
/**
 * PopSpider PHP CLI script
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.TXT.
 * It is also available through the world-wide-web at this URL:
 * https://github.com/nicksagona/PopSpider/blob/master/LICENSE.TXT
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@popphp.org so we can send you a copy immediately.
 *
 * Possible usage and arguments
 *
 * ./crawl http://www.domain.com/ -d ./myfolder -o csv -e b,u
 *
 * -d --dir folder         Set the folder in which to output the file(s) (default: current)
 * -o --output html|csv    Set the output to either HTML or CSV (default: HTML)
 * -e --elements b,u       Set any additional elements to parse, comma-separated list
 * -h --help               Display this help
 *
 * IMPORTANT!
 *
 * If you move the 'bootstrap.php' file, make
 * sure you adjust the path to it accordingly
 *
 */

set_time_limit(0);

require_once __DIR__ . '/../bootstrap.php';

use PopSpider\Crawler;

$url = null;
$output = 'html';
$folder = __DIR__;
$elements = null;

// Write header
echo PHP_EOL;
echo 'PopSpider CLI' . PHP_EOL;
echo '=============' . PHP_EOL . PHP_EOL;

//print_r($argv);

// Display help
if (isset($argv[1]) && (($argv[1] == '-h') || ($argv[1] == '--help'))) {
    echo 'Help' . PHP_EOL;
    echo '----' . PHP_EOL;
    echo ' -d --dir folder         Set the folder in which to output the file(s) (default: current)' . PHP_EOL;
    echo ' -o --output html|csv    Set the output to either HTML or CSV (default: HTML)' . PHP_EOL;
    echo ' -e --elements b,u       Set any additional elements to parse, comma-separated list' . PHP_EOL;
    echo ' -h --help               Display this help' . PHP_EOL . PHP_EOL;
    exit(0);
}

// Check for a URL parameter
if (!isset($argv[1])) {
    echo 'You must pass at least a valid URL to crawl. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
    exit(0);
}

// Check for a valid URL
if (isset($argv[1]) && (substr($argv[1], 0, 4) != 'http')) {
    echo 'The URL must be a valid URL, i.e. http://domain.com/. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
    exit(0);
}

$url = $argv[1];

// Get the output directory
if (in_array('-d', $argv) || in_array('--dir', $argv)) {
    $i = (in_array('-d', $argv)) ? array_search('-d', $argv) : array_search('--dir', $argv);
    $i++;
    if (!isset($argv[$i])) {
        echo 'You must pass a folder argument. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
        exit(0);
    } else {
        $dir = __DIR__ . DIRECTORY_SEPARATOR . $argv[$i];
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $folder = realpath($dir);
    }
}

// Get the output format
if (in_array('-o', $argv) || in_array('--output', $argv)) {
    $i = (in_array('-o', $argv)) ? array_search('-o', $argv) : array_search('--output', $argv);
    $i++;
    if (!isset($argv[$i])) {
        echo 'You must pass an output argument. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
        exit(0);
    } else {
        $output = strtolower($argv[$i]);
        if (($output != 'html') && ($output != 'csv')) {
            echo 'The output argument must only be either \'html\' or \'csv\'. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
            exit(0);
        }
    }
}

// Get the additional elements
if (in_array('-e', $argv) || in_array('--elements', $argv)) {
    $i = (in_array('-e', $argv)) ? array_search('-e', $argv) : array_search('--elements', $argv);
    $i++;
    if (!isset($argv[$i])) {
        echo 'You must pass an elements argument. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
        exit(0);
    } else {
        $elements = explode(',', $argv[$i]);
    }
}

echo 'URL: ' . $url . PHP_EOL;
echo 'Output: ' . $output . PHP_EOL;
echo 'Dir: ' . $folder . PHP_EOL;
echo 'elements: ' . var_export($elements, true) . PHP_EOL . PHP_EOL;


Crawler::crawl($url);

$urls = Crawler::getUrls();
$errors = Crawler::getErrors();

print_r($urls);

foreach ($urls as $key => $value) {
    echo '(' . $value->getCode() . ') ' . $key . PHP_EOL;
}

echo PHP_EOL . PHP_EOL;

print_r($errors);

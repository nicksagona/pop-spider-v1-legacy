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
 * ./crawl -u http://www.domain.com/ -d ./folder -e b,u
 *
 * -u --url http://www.domain.com/    Set the URL in which to crawl
 * -d --dir folder                    Set the folder in which to output the file(s)
 * -e --elements b,u                  Set any additional elements to parse, comma-separated list
 * -h --help                          Display this help
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

try {
    $start = time();
    $options = getopt('u:d:e:h', array('url:', 'dir:', 'elements:', 'help'));
    $url = null;
    $folder = __DIR__;
    $elements = null;

    // Write header
    echo PHP_EOL;
    echo 'PopSpider' . PHP_EOL;
    echo '=========' . PHP_EOL . PHP_EOL;

    // Display help
    if (isset($options['h']) || isset($options['help'])) {
        echo 'Help' . PHP_EOL;
        echo '----' . PHP_EOL;
        echo ' -u --url http://www.domain.com/    Set the URL in which to crawl' . PHP_EOL;
        echo ' -d --dir folder                    Set the folder in which to output the file(s) (default: current)' . PHP_EOL;
        echo ' -e --elements b,u                  Set any additional elements to parse, comma-separated list' . PHP_EOL;
        echo ' -h --help                          Display this help' . PHP_EOL . PHP_EOL;
        exit(0);
    }

    // Check for a URL parameter
    if (!isset($options['u']) && !isset($options['url'])) {
        echo 'You must pass at least a valid URL to crawl. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
        exit(0);
    } else {
        $url = (isset($options['u'])) ? $options['u'] : $options['url'];
    }

    // Check for a valid URL
    if (substr($url, 0, 4) != 'http') {
        echo 'The URL must be a valid URL, i.e. http://www.domain.com/. ./crawl --help for help.' . PHP_EOL . PHP_EOL;
        exit(0);
    }

    // Get the output directory
    if (isset($options['d']) || isset($options['dir'])) {
        $dir = (isset($options['d'])) ? $options['d'] : $options['dir'];
        if ((substr($dir, 0, 1) != '/') && (substr($dir, 1, 1) != ':')) {
            $dir = __DIR__ . DIRECTORY_SEPARATOR . $dir;
        }
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $folder = realpath($dir);
    }


    // Get the additional elements
    if (isset($options['e']) || isset($options['elements'])) {
        $elements = (isset($options['e'])) ? $options['e'] : $options['elements'];
        $elements = explode(',', $elements);
    }

    echo 'Crawling: ' . $url . PHP_EOL;
    echo '----------' . str_repeat('-', strlen($url)) . PHP_EOL;

    Crawler::crawl($url, $elements);
    Crawler::output($url, $folder);

    $elapsedTime = null;
    $time = time() - $start;
    $secs = $time % 60;
    $mins = ($time - $secs) / 60;

    echo PHP_EOL . 'Done (' . $mins . ':' . $secs . ')' . PHP_EOL . PHP_EOL;
} catch (\Exception $e) {
    echo $e->getMessage() . PHP_EOL . PHP_EOL;
}

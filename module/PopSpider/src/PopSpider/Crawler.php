<?php

/**
 * @namespace
 */
namespace PopSpider;

/**
 * This is the Spider class for the PopSpider.
 *
 * @category   PopSpider
 * @package    PopSpider
 * @author     Nick Sagona, III <nick@popphp.org>
 * @copyright  Copyright (c) 2009-2012 Moc 10 Media, LLC. (http://www.moc10media.com)
 * @license    https://github.com/nicksagona/PopSpider/blob/master/LICENSE.TXT     New BSD License
 * @version    1.0
 */

use Pop\Data\Data,
    Pop\File\File,
    Pop\Mvc\Model,
    Pop\Mvc\View;

class Crawler
{

    /**
     * URLs
     * @var array
     */
    protected static $urls = array();

    /**
     * Errors
     * @var array
     */
    protected static $errors = array();

    /**
     * Static method to crawl the URLs
     *
     * @param  string $url
     * @param  array  $elements
     * @param  string $parent
     * @return void
     */
    public static function crawl($url, $elements = null, $parent = null)
    {
        if (!array_key_exists($url, self::$urls)) {
            $spider = new Spider($url, $elements);
            echo '-> (' . $spider->getCode() . ') ' . $url . PHP_EOL;
            if ($spider->isError()) {
                self::$errors[] = array(
                    'url'    => $url,
                    'code'   => $spider->getCode(),
                    'parent' => $parent
                );
            } else {
                self::$urls[$url] = $spider;
                $domain = str_replace(self::$urls[$url]->getSchema(), '', self::$urls[$url]->getBase());
                if (strpos($domain, '/') !== false) {
                    $domain = substr($domain, 0, strpos($domain, '/'));
                }
                $urls = self::$urls[$url]->getElements('a');
                if (null !== $urls) {
                    foreach ($urls as $u) {
                        if ((null !== $u['href']) && ($u['href'] != '') && ($u['href'] != '#') && (stripos($u['href'], $domain) !== false)) {
                            self::crawl($u['href'], $elements, $url);
                        }
                    }
                }
            }
        }
    }

    /**
     * Static method to output the results
     *
     * @param  string $url
     * @param  string $output
     * @param  string $dir
     * @return void
     */
    public static function output($url, $output, $dir)
    {
        if ($output == 'csv') {
            echo 'Do some CSV stuff';
        } else {
            $view = View::factory(__DIR__ . '/../../view/index.phtml', new Model(array(
                'title'  => $url,
                'urls'   => self::$urls,
                'errors' => self::$errors
            )));
            $html = $view->render(true);
            copy(__DIR__ . '/../../data/styles.css', $dir . DIRECTORY_SEPARATOR . 'styles.css');
            $file = new File($dir . DIRECTORY_SEPARATOR . 'index.html');
            $file->write($html)
                 ->save();
        }
    }

    /**
     * Static method to get the URLs
     *
     * @return array
     */
    public static function getUrls()
    {
        return self::$urls;
    }

    /**
     * Static method to get the errors
     *
     * @return array
     */
    public static function getErrors()
    {
        return self::$errors;
    }

}

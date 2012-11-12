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
     * @return void
     */
    public static function crawl($url, $parent = null)
    {
        if (!array_key_exists($url, self::$urls)) {
            $spider = new Spider($url);
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
                            self::crawl($u['href'], $url);
                        }
                    }
                }
            }
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

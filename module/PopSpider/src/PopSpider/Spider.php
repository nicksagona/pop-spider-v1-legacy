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

use Pop\Http\Response;

class Spider
{

    /**
     * Document URL
     * @var string
     */
    protected $url = null;

    /**
     * Document schema
     * @var string
     */
    protected $schema = null;

    /**
     * Document base
     * @var string
     */
    protected $base = null;

    /**
     * Response error
     * @var boolean
     */
    protected $error = false;

    /**
     * Response code
     * @var string
     */
    protected $code = null;

    /**
     * Document content type
     * @var string
     */
    protected $contentType = null;

    /**
     * DOMDocument object
     * @var DOMDocument
     */
    protected $dom = null;

    /**
     * DOMDocument object
     * @var array
     */
    protected $elements = array(
        'title'  => null,
        'meta'   => array(), // name, content
        'a'      => array(), // href, title, rel, name, value
        'img'    => array(), // src, title, alt
        'h1'     => array(),
        'h2'     => array(),
        'h3'     => array(),
        'h4'     => array(),
        'h5'     => array(),
        'h6'     => array(),
        'strong' => array(),
        'em'     => array()
    );

    /**
     * Constructor
     *
     * Instantiate the document object
     *
     * @param  string $url
     * @param  array  $elements
     * @return void
     */
    public function __construct($url, array $elements = null)
    {
        $this->url = $url;
        $this->schema = substr($this->url, 0, (strpos($this->url, '//') + 2));

        $this->base = str_replace($this->schema, '', $this->url);
        if (substr($this->base, -1) == '/') {
            $this->base = substr($this->base, 0, -1);
        }
        if (strpos($this->base, '/') !== false) {
            $base = substr($this->base, 0, (strrpos($this->base, '/') + 1));
            $tail = substr($this->base, (strrpos($this->base, '/') + 1));
            if (strpos($tail, '.') === false) {
                $this->base = $base . $tail;
            } else {
                $this->base = $base;
            }
        }

        $this->base = $this->schema . $this->base;

        if (substr($this->base, -1) != '/') {
            $this->base .= '/';
        }

        $ua = (isset($_SERVER['HTTP_USER_AGENT'])) ?
            $_SERVER['HTTP_USER_AGENT'] :
            'Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:16.0) Gecko/20100101 Firefox/16.0';

        $opts = array(
            'http' => array(
                'method'     => 'GET',
                'header'     => "Accept-language: en\r\n" . "User-Agent: " . $ua . "\r\n",
                'user_agent' => $ua
            )
        );

        $response = Response::parse($this->url, stream_context_create($opts));
        $this->error = $response->isError();
        $this->code = $response->getCode();

        if (!$this->error) {
            // Get content type
            if (null !== $response->getHeader('Content-type')) {
                $this->contentType = $response->getHeader('Content-type');
            } else if (null !== $response->getHeader('Content-Type')) {
                $this->contentType = $response->getHeader('Content-Type');
            }

            // If an HTML page, parse it
            if ($this->contentType == 'text/html') {
                $oldError = ini_get('error_reporting');
                error_reporting(0);

                $this->dom = new \DOMDocument();
                $this->dom->strictErrorChecking = false;
                $this->dom->loadHTML($response->getBody());

                error_reporting($oldError);

                $this->parseElements($elements);
            }
        }

    }

    /**
     * Get the URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Get the schema
     *
     * @return string
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * Get the base
     *
     * @return string
     */
    public function getBase()
    {
        return $this->base;
    }

    /**
     * Get the content type
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Get the response error
     *
     * @return boolean
     */
    public function isError()
    {
        return $this->error;
    }

    /**
     * Get the response code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Get the dom object
     *
     * @return DOMDocument
     */
    public function getDom()
    {
        return $this->dom;
    }

    /**
     * Get the element count by name
     *
     * @param  string $name
     * @return int
     */
    public function count($name)
    {
        return (array_key_exists($name, $this->elements)) ? count($this->elements[$name]) : 0;
    }

    /**
     * Get the elements by name
     *
     * @param  string $name
     * @return mixed
     */
    public function getElements($name = null)
    {
        if (null === $name) {
            return $this->elements;
        } else {
            return (array_key_exists($name, $this->elements)) ? $this->elements[$name] : null;
        }
    }

    /**
     * Parse the documents elements
     *
     * @param  array $elements
     * @return void
     */
    protected function parseElements(array $elements = null)
    {
        // Get TITLE tag
        $title = $this->dom->getElementsByTagName('title');

        if (null !== $title->item(0)) {
            $this->elements['title'] = $title->item(0)->nodeValue;
        }

        // Get META tags
        $meta = $this->dom->getElementsByTagName('meta');

        if (null !== $meta->item(0)) {
            foreach ($meta as $m) {
                if ($m->hasAttribute('name') && $m->hasAttribute('content')) {
                    $this->elements['meta'][] = array(
                        'name'    => $m->getAttribute('name'),
                        'content' => $m->getAttribute('content')
                    );
                }
            }
        }

        // Get A tags
        $anchors = $this->dom->getElementsByTagName('a');

        if (null !== $anchors->item(0)) {
            foreach ($anchors as $a) {
                // Determine if the node value is a string or an image
                if ($a->nodeValue != '') {
                    $value = $a->nodeValue;
                } else {
                    $imgs = $a->getElementsByTagName('img');
                    $value = (null !== $imgs->item(0)) ? '[image]' : null;
                }

                // Get the HREF attribute.
                $href = null;

                // If the HREF attribute is relative, parse and format the absolute URL
                if (($a->hasAttribute('href')) &&
                    ($a->getAttribute('href') != '#') &&
                    ($a->getAttribute('href') != '') &&
                    (substr(strtolower($a->getAttribute('href')), 0, 5) != 'http:') &&
                    (substr(strtolower($a->getAttribute('href')), 0, 6) != 'https:') &&
                    (substr(strtolower($a->getAttribute('href')), 0, 7) != 'mailto:') &&
                    (substr(strtolower($a->getAttribute('href')), 0, 4) != 'tel:')) {
                    $base = (substr($this->base, -1) != '/') ? $this->base . '/' : $this->base;
                    $h = $a->getAttribute('href');
                    if (substr($h, 0, 1) == '/') {
                        $h = substr($h, 1);
                        $base = str_replace($this->schema, '', $base);
                        if (strpos($base, '/') !== false) {
                            $base = substr($base, 0, strpos($base, '/'));
                        }
                        $href = $this->schema . $base . '/' . $h;
                    } else if (substr($h, 0, 2) == './') {
                        $h = substr($h, 2);
                        $href = $base . $h;
                    } else if (strpos($h, '../') !== false) {
                        $num = substr_count($h, '../');
                        $base = substr($base, 0, -1);
                        $base = str_replace($this->schema, '', $base);
                        if (strpos($base, '/') !== false) {
                            for ($i = 0; $i < $num; $i++) {
                                $base = substr($base, 0, strrpos($base, '/'));
                            }
                        }
                        $base = $this->schema . $base . '/';
                        $h = str_replace('../', '', $h);
                        $href = $base . $h;
                    } else {
                        $href = $base . $h;
                    }
                // Else, use the full HREF attribute URL
                } else if (($a->hasAttribute('href')) && (substr(strtolower($a->getAttribute('href')), 0, 4) == 'http')) {
                    $href = $a->getAttribute('href');
                }

                $this->elements['a'][] = array(
                    'href'  => $href,
                    'title' => ($a->hasAttribute('title') ? $a->getAttribute('title') : null),
                    'name'  => ($a->hasAttribute('name') ? $a->getAttribute('name') : null),
                    'rel'   => ($a->hasAttribute('rel') ? $a->getAttribute('rel') : null),
                    'value' => $value
                );
            }
        }

        // Get IMG tags
        $imgs = $this->dom->getElementsByTagName('img');

        if (null !== $imgs->item(0)) {
            foreach ($imgs as $img) {
                $this->elements['img'][] = array(
                    'src'   => ($img->hasAttribute('src') ? $img->getAttribute('src') : null),
                    'alt'   => ($img->hasAttribute('alt') ? $img->getAttribute('alt') : null),
                    'title' => ($img->hasAttribute('title') ? $img->getAttribute('title') : null),
                );
            }
        }

        // Get H# tags
        for ($i = 1; $i < 7; $i++) {
            $headers = $this->dom->getElementsByTagName('h' . $i);
            if (null !== $headers->item(0)) {
                foreach ($headers as $header) {
                    $this->elements['h' . $i][] = $header->nodeValue;
                }
            }
        }

        // Get STRONG tags
        $strong = $this->dom->getElementsByTagName('strong');

        if (null !== $strong->item(0)) {
            foreach ($strong as $s) {
                $this->elements['strong'][] = $s->nodeValue;
            }
        }

        // Get EM tags
        $em = $this->dom->getElementsByTagName('em');

        if (null !== $em->item(0)) {
            foreach ($em as $e) {
                $this->elements['em'][] = $e->nodeValue;
            }
        }

        // Get any additional elements
        if (null !== $elements) {
            foreach ($elements as $element) {
                $elem = $this->dom->getElementsByTagName($element);

                if (null !== $elem->item(0)) {
                    $this->elements[$element] = array();
                    foreach ($elem as $e) {
                        $this->elements[$element][] = $e->nodeValue;
                    }
                }
            }
        }

    }

}
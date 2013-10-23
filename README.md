PopSpider 1.1.1
===============

RELEASE INFORMATION
-------------------
PopSpider 1.1.1 Release  
Released October 23, 2013

A simple web spider that parses SEO-pertinent data from a website
and produces a HTML-based report of what was parsed as well as
a sitemap.xml file.

By default, the spider parses the following elements and their
SEO-pertinent attributes:
* title
* meta
    + name & content
* a
    + href, title, rel, name & value
* img
    + src, title & alt
* h1
* h2
* h3
* h4
* h5
* h6
* strong
* em

You can parse additional elements via the --elements option.

Options:
--------
* -u --url http://www.domain.com/
    - Set the URL in which to crawl
* -d --dir folder
    - Set the folder in which to output the file(s)
* -e --elements b,u
    - Set any additional elements to parse, comma-separated list
* -h --help
    - Display this help

Basic CLI usage:
----------------

    scripts/crawl -u http://www.mydomain.com/ -d ./myfolder -e b,u

CHANGELOG:
----------
* 1.1.1
    - Update to Pop 1.6.0

* 1.1.0
    - Update to Pop 1.5.0
    - Move PopSpider into vendor folder
    - Add sitemap
    - Add timer

* 1.0.3
    - Update to Pop 1.4.0, add two more html entities to exclude from the URL parser.

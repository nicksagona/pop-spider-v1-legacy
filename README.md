PopSpider 1.0.2
===============

RELEASE INFORMATION
-------------------
PopSpider 1.0.2 Release
Released March 6, 2013

A simple web spider that parses SEO-pertinent data from a website.
Simply download this Pop module, hook it up to a local copy of the
Pop PHP Framework and it'll be ready to crawl any site.

By default, the spider parses the following elements and their
SEO-pertinent attributes:
* title
* meta
* a
* img
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

    scripts/crawl -u http://www.domain.com/ -d ./folder -e b,u

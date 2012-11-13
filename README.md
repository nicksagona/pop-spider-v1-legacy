PopSpider
=========

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
<pre>
scripts/crawl -u http://www.domain.com/ -d ./myfolder  -e b,u
</pre>
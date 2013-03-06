@echo off
REM
REM PopSpider Windows CLI script
REM
REM LICENSE
REM
REM This source file is subject to the new BSD license that is bundled
REM with this package in the file LICENSE.TXT.
REM It is also available through the world-wide-web at this URL:
REM https://github.com/nicksagona/PopSpider/blob/master/LICENSE.TXT
REM If you did not receive a copy of the license and are unable to
REM obtain it through the world-wide-web, please send an email
REM to info@popphp.org so we can send you a copy immediately.
REM
REM Possible usage and arguments
REM
REM ./crawl -u http://www.domain.com/ -d ./folder -e b,u
REM
REM -u --url http://www.domain.com/    Set the URL in which to crawl
REM -d --dir folder                    Set the folder in which to output the file(s) (default: current)
REM -e --elements b,u                  Set any additional elements to parse, comma-separated list
REM -h --help                          Display this help
REM

SET SCRIPT_DIR=%~dp0
php %SCRIPT_DIR%crawl.php %*
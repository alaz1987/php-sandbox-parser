# Test html table parser

1. Task #1 (Download data from remote server)
2. Task #2 (Parse html table and save to xml file)

[More details](http://cbonds.info/sandbox/some_source.php?redirect=1)

## Requirements

1. [PHP 5.6+](https://www.php.net/releases/#5.6.40)
2. [curl](http://php.net/manual/en/book.curl.php)
3. [PCRE](http://php.net/manual/en/book.pcre.php)
4. [DOM](http://php.net/manual/en/book.dom.php)

## Usage

### Docker-compose

`$ docker-compose up`

View data in `./data` directory.

### Manual

`$ mkdir ./src/data && php ./src/run.php`

View data in `./src/data` directory.
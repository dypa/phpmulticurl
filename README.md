# PhpMultiCurl

[![Latest Stable Version](https://poser.pugx.org/dypa/phpmulticurl/v/stable.png)](https://packagist.org/packages/dypa/phpmulticurl)
[![License](https://poser.pugx.org/dypa/phpmulticurl/license.png)](https://packagist.org/packages/dypa/phpmulticurl)
[![Total Downloads](https://poser.pugx.org/dypa/phpmulticurl/downloads.png)](https://packagist.org/packages/dypa/phpmulticurl)

Ultra fast non-blocking OOP wrapper for `curl_multi_*` functions.

__Pull requests are very welcome.__

## Main features:

* **reuse curl resource**
* don't waste time on unnecessary cycles, careful works with select function
* simple queue management
* fully configured! supports callbacks onLoad, onError, full control on http headers
* simple usage
* no tests, docs :( sorry :(

## Requires:

* php >= 7.3.10
* ext-curl
* safe_mode = Off

## Installation via composer:

* install composer
* run `composer require dypa/phpMultiCurl`

## Examples

* [load many urls parallel](https://github.com/dypa/phpmulticurl/blob/master/examples/example0.php)
* [working with options and response](https://github.com/dypa/phpmulticurl/blob/master/examples/example1.php)
* [load urls in callbacks](https://github.com/dypa/phpmulticurl/blob/master/examples/example2.php)

##Contributing

Fork the project, create a feature branch and send us a pull request.

To ensure a consistent code base, you should make sure the code follows
the [PSR-*](http://www.php-fig.org/psr/) coding standards.

To avoid CS issues, you should use [php-cs-fixer](http://cs.sensiolabs.org/):

```sh
$ php-cs-fixer fix src/
```

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

## Requires:

* php >= 5.3.0 (but **recomended version is php >=5.5.0**, __it will give x4 perfomance boost__)
* ext-curl
* safe_mode = Off

## Installation via composer:

* install composer
* add in __require__ section `dypa/phpMultiCurl`
* run `composer install`

## Examples

* [load many urls parallel](https://github.com/dypa/phpmulticurl/blob/master/examples/example0.php)
* [working with options and response](https://github.com/dypa/phpmulticurl/blob/master/examples/example1.php)
* [load urls in callbacks](https://github.com/dypa/phpmulticurl/blob/master/examples/example2.php)
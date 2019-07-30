[![Latest Stable Version](https://poser.pugx.org/zozlak/http-accept/v/stable)](https://packagist.org/packages/zozlak/http-accept)
[![Build Status](https://travis-ci.org/zozlak/httpAccept.svg?branch=master)](https://travis-ci.org/zozlak/httpAccept)
[![Coverage Status](https://coveralls.io/repos/github/zozlak/httpAccept/badge.svg?branch=master)](https://coveralls.io/github/zozlak/httpAccept?branch=master)
[![License](https://poser.pugx.org/zozlak/http-accept/license)](https://packagist.org/packages/zozlak/http-accept)

# HttpAccept

A static class making it easier to deal with the HTTP Accept header.

## installation

`composer require zozlak/http-accept`

## usage

```php
$bestMatch = zozlak\HttpAccept::getBestMatch(['application/json', 'application/xml']);
echo (string) $bestMatch . "\n";
// e.g. for an Accept header of
//   text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8'
// you will get `application/xml;q=0.9`
```


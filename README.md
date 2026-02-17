[![Latest Stable Version](https://poser.pugx.org/zozlak/http-accept/v/stable)](https://packagist.org/packages/zozlak/http-accept)
[![Build Status](https://travis-ci.org/zozlak/httpAccept.svg?branch=master)](https://travis-ci.org/zozlak/httpAccept)
[![Coverage Status](https://coveralls.io/repos/github/zozlak/httpAccept/badge.svg?branch=master)](https://coveralls.io/github/zozlak/httpAccept?branch=master)
[![License](https://poser.pugx.org/zozlak/http-accept/license)](https://packagist.org/packages/zozlak/http-accept)

# HttpAccept

[![Latest Stable Version](https://poser.pugx.org/zozlak/httpAccept/v/stable)](https://packagist.org/packages/zozlak/httpAccept)
![Build status](https://github.com/zozlak/quickRdf/workflows/phpunit/badge.svg?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/zozlak/httpaccept/badge.svg?branch=master)](https://coveralls.io/github/zozlak/quickRdf?branch=master)
[![License](https://poser.pugx.org/zozlak/httpA-accept/license)](https://packagist.org/packages/zozlak/http-accept)

A static class making it easier to deal with the HTTP Accept header.

Can be also used to deal with other HTTP headers which provide multiple options with weights,
e.g. the Accept-Encoding one.

## installation

`composer require zozlak/http-accept`

## usage

```php
// Simplest use - parse the HTTP Accept header
// e.g. for an Accept header of
//   text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8
// the result is `application/xml;q=0.9`
$header = zozlak\httpAccept\Accept::fromHeader();
$bestMatch = $header->getBestMatch(['application/json', 'application/xml']);
echo (string) $bestMatch . "\n";

// Deal with Accept-Encoding
$header = zozlak\httpAccept\Accept::fromHeader('accept-encoding');
try {
    $bestMatch = $header->getBestMatch(['deflate']);
} catch (zozlak\httpAccept\NoMatchException) {
    $bestMatch = new zozlak\httpAccept\Format('identity');
}
echo $bestMatch->type . "\n";

// Check if two formats match
$format1 = zozlak\httpAccept\Format::fromString('application/xml');
$format2 = new zozlak\httpAccept\Format('application', '*');
var_dump($format1->matches($format2));
```


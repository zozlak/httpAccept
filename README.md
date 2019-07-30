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


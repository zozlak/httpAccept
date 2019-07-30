# HttpAccept

A static class making it easier to deal with the HTTP Accept header.

## installation

`composer require zozlak/httpAccept`

## usage

```
$_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8';
$bestMatch = zozlak\HttpAccept::getBestMatch(['application/json', 'application/xml']);
echo (string) $bestMatch . "\n";
```


# Flourish/Url
## A URL parsing and manipulation class

Creating a new `Url` object can be done using the existing URL or any URL you define:

```php
$url = new Url();
```

```php
$url = new Url('http://www.example.com');
```

In the case of the former, the `Url` class will determine as much as possible about the existing request URL.  It does this primarily using the `$_SERVER` super global, however, in the event that you're running in CLI and have not overloaded this value, some sane defaults will be used.

### Getting Data

When you create a new `Url` object the class will parse the URL into separate data components similar to how PHP's `parseurl` does it.  You can access these pieces with a number of methods:

- `getScheme()`
- `getHost()`
- `getPort()`
- `getPath()`
- `getQuery()`
- `getFragment()`

In addition to these basic methods, you can also use some methods which combine different pieces.  For example, if you need to know the full "domain" of the request you can use the `getDomain()` method:

```php
$url = new Url('https://www.example.com:445/path/subpath/file.html');

echo $url->getDomain();

//
// OUTPUT:
//
// https://www.example.com:445
//
```

The domain includes not only the host but also the full scheme and port (if not the default for the scheme).  Similar, if you need the equivalent of PHP's `$_SERVER['REQUEST_URI']` which includes the query string, you'll probably want to use `getPathWithQuery()`.

```php
$url = new Url('http://example.com/path/subpath/?param1=value&param2=other_value');

echo $url->getPathWithQuery()

//
// OUTPUT:
//
// /path/subpath?param1=value&param2=other_value
//
```

### Normalization

The `Url` class also attempts to normalize data as much as possible so that you can see consistent and expected results.  For example, if you provide a URL with a scheme of `https` and a port of `443` the port will be discarded as it is the default for the scheme.  For this reason you can always expect `getPort()` to return `NULL` in the event it provides no significant information.

Similarly, it is possible to pass a URL with a query `encoded+like+this`, however, you should always expect the queries you get out of `Url` to be `encoded%20like%20this`.

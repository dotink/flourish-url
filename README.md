A URL parsing and manipulation class
=======

This class is designed to provide an extremely easy interface for parsing and modifying URLs.  It
is useful in many cases where you want to modify the current URL in a very specific way, for
example, redirecting a user to HTTPS:

```php
$url = new Url();

header('Location: ' . $url->modify(['scheme' => 'https']));
```

But can also be used to easily create relative links to the current URL:

```php
$url = new Url();
$url = $url->modify('../../related_file.html');
```

To get an idea on basic usage, check out the [class documentation](./docs/url.md)

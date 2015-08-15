# Cache helpers for PSR-7 HTTP Messages

[![Build Status](https://secure.travis-ci.org/micheh/psr7-cache.svg?branch=master)](https://secure.travis-ci.org/micheh/psr7-cache)
[![codecov.io](http://codecov.io/github/micheh/psr7-cache/coverage.svg?branch=master)](http://codecov.io/github/micheh/psr7-cache?branch=master)

This library provides an easy way to either add cache relevant headers to a PSR-7 HTTP message implementation, or to extract cache information from a PSR-7 message (e.g. if a response is cacheable).


## Installation

Install this library using composer:

```console
$ composer require micheh/psr7-cache
```

## Quickstart

To enable caching of HTTP responses, create an instance of `CacheUtil`, call the method `withCache` and provide your PSR-7 response.

```php
/** @var \Psr\Http\Message\ResponseInterface $response */

$util = new \Micheh\Cache\CacheUtil();
$response = $util->withCache($response);
```

This will add the header `Cache-Control: private, max-age=600` to your response.
With this header the response will only be cached by the person who sent the request and will be cached for 600 seconds (10 min).

### Cache Validators
After the specified 10 minutes the cache is expired. The client will make a new request to the application and get the newest version.
You should also add an `ETag` header (and `Last-Modified` header if you know when the resource was last modified) so that the application does not have to send the response again in case the client already has the current version (Cache Validation).

```php
/** @var \Psr\Http\Message\ResponseInterface $response */

$util = new \Micheh\Cache\CacheUtil();
$response = $util->withCache($response);
$response = $util->withETag($response, 'my-etag');
$response = $util->withLastModified($response, '2015-08-16 16:31:12');
```

### Revalidate a response
To determine if the client still has a current copy of the page and the response is not modified, you can use the `isNotModified` method.
Add only the cache headers to the response and then compare the request with the response.
If the response is not modified, return the empty response with a status code 304.
Keep the code before the `isNotModified` call as lightweight as possible to increase performance.
Don't create the complete response before the method.

```php
/** @var \Psr\Http\Message\RequestInterface $request */
/** @var \Psr\Http\Message\ResponseInterface $response */

$util = new \Micheh\Cache\CacheUtil();
$response = $util->withCache($response);
$response = $util->withETag($response, 'my-etag');
$response = $util->withLastModified($response, '2015-08-16 16:31:12');

if ($util->isNotModified($request, $response)) {
    return $response->withStatus(304);
}

// create the body of the response
```


## Available methods

Method                | Description (see the phpDoc for more information)
--------------------- | ------------------------------------------------------------------------
`withCache`           | Convenience method to add a `Cache-Control` header, which allows caching
`withCachePrevention` | Convenience method to prevent caching
`withExpires`         | Adds an `Expires` header (date can be absolute or relative)
`withETag`            | Adds an `ETag` header
`withLastModified`    | Adds a `Last-Modified` header (date can be absolute or relative)
`withCacheControl`    | Adds a `Cache-Control` header with the provided directives (from array)
`isNotModified`       | Checks if a response is not modified
`isCacheable`         | Checks if a response is cacheable by a public cache
`isFresh`             | Checks if a response is fresh (age smaller than lifetime)
`getLifetime`         | Returns the lifetime of a response (how long it should be cached)
`getAge`              | Returns the age of a response (how long it was in the cache)


## References

- [RFC7234: Caching](https://tools.ietf.org/html/rfc7234)
- [RFC7232: Conditional Requests](https://tools.ietf.org/html/rfc7232) (Cache Validation)
- [RFC5861: Cache-Control Extensions for Stale Content](https://tools.ietf.org/html/rfc5861)


## License

The files in this archive are licensed under the BSD-3-Clause license.
You can find a copy of this license in [LICENSE.md](LICENSE.md).

# sndsgd/http

[![Latest Version](https://img.shields.io/github/release/sndsgd/http.svg?style=flat-square)](https://github.com/sndsgd/http/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](https://github.com/sndsgd/http/LICENSE)
[![Build Status](https://img.shields.io/travis/sndsgd/http/master.svg?style=flat-square)](https://travis-ci.org/sndsgd/http)
[![Coverage Status](https://img.shields.io/coveralls/sndsgd/http.svg?style=flat-square)](https://coveralls.io/r/sndsgd/http?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/sndsgd/http.svg?style=flat-square)](https://packagist.org/packages/sndsgd/http)

Improved HTTP for PHP.


## Requirements

This project is unstable and subject to changes from release to release.

You need **PHP >= 7.1** to use this library, however, the latest stable version of PHP is recommended.


## Install

Install `sndsgd/http` using [Composer](https://getcomposer.org/).


## Usage

This library aims to improve how PHP handles HTTP requests, mainly by streamlining the the process of working with request parameters.

### Improved Request Parameter Decoders

To experiment with the decoders, you can use PHP's built in webserver with the script located in `bin/request-demo.php`.

```sh
php -S localhost:8000 bin/request-demo.php
```

Now you will be able to make requests to [http://localhost:8000](http://localhost:8000/) using any HTTP client. In the examples below, we'll be using [httpie](https://github.com/jkbrzt/httpie). To dump information about the request that is being made, simply append `-v` to any of the commands below.


#### Query String Decoder

In many query parameter implementations, you do not need to use brackets to indicate multiple values, but with PHP you do. For compatibility with the built in PHP decoder, you can continue to use brackets.

```sh
http http://localhost:8000/query a==1 a==2 a[]==💩
```

Result:

```json
    "$_GET": {
        "a": [
            "💩"
        ]
    },
    "sndsgd": {
        "a": [
            "1",
            "2",
            "💩"
        ]
    }
}
```

#### Request Body Decoder

The built in body decoder for PHP only handles `multipart/form-data` and `application/x-www-form-urlencoded` content types for `POST` requests. This library acts as a polyfill to add `application/json` to that list, and to allow for decoding the request body for _any_ request method. By default, `POST` requests will be processed by the built in PHP decoder, however, you can disable that functionality by setting `enable_post_data_reading = Off` in your `php.ini`.


> _The built in decoder will not decode the body of this urlencoded `PATCH` request_

```
http --form PATCH http://localhost:8000/body a=1 b=2 c[d]=3a
```

Result:

```json
{
    "$_POST": [],
    "$_FILES": [],
    "sndsgd": {
        "a": "1",
        "b": "2",
        "c": {
            "d": "3"
        }
    }
}
```

#### Uploaded Files

Instead of using `$_FILES`, with `sndsgd/http` uploaded files are included with the other parameters of the request body as objects.

```
http --form POST http://localhost:8000/body a=1 file@README.md
```

Result:

```json
{
    "$_POST": {
        "a": "1"
    },
    "$_FILES": {
        "file": {
            "name": "README.md",
            "type": "",
            "tmp_name": "/private/var/folders/2b/mtmy5wk56jx13vjgqpydc3nr0000gn/T/php9DTXiq",
            "error": 0,
            "size": 2766
        }
    },
    "sndsgd": {
        "a": "1",
        "file": {
            "filename": "README.md",
            "contentType": "text/x-markdown",
            "realContentType": "text/plain",
            "size": 2766,
            "tempfile": "/private/var/folders/2b/mtmy5wk56jx13vjgqpydc3nr0000gn/T/php9DTXiq"
        }
    }
}
```

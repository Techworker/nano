nano
=====

[![Build Status](https://travis-ci.org/Techworker/nano.png)](https://travis-ci.org/Techworker/nano)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Techworker/nano/badges/quality-score.png?s=b61d7b3ff68cad9ede06c7574177b672458f80a9)](https://scrutinizer-ci.com/g/Techworker/nano/)
[![Code Coverage](https://scrutinizer-ci.com/g/Techworker/nano/badges/coverage.png?s=8f0b002ed08a392d9c7a8ea871edd367825be26a)](https://scrutinizer-ci.com/g/Techworker/nano/)

A code readability promoting, placeholder oriented, less defective sprintf functionality, inspired by https://github.com/trix/nano.
It is, of course, up to you if you want accept the overhead compared to the PHP core functionality. I use it to format logging messages or Exceptions, which means mostly speed-independant code-parts. Try it out, it is small, well documented and tested.

### About

This class tries to avoid manual string concenation by replacing code snippets like the following:

```php
echo "The Parameter " . $param . " with value " . $value . " in method " . 
     __CLASS__ . ":" . __FUNCTION__ . " was simply wrong.";
```

```php
echo sprintf("The Parameter %s with value %s in method %s:%s was simply wrong.", 
    $param, $value, __CLASS__, __METHOD__
);
```

..with..

```php
$message = "The Parameter {param} with value {value} in method {class}:{method} was simply wrong";
echo (new Nano($message))->data([
    'param' => $param,
    'value' => $value,
    'method' => __METHOD__,
    'class' => __CLASS__
]);
```

### Documentation

You have multiple ways to use the `Techworker\nano` class, but at first you have to install it. At best via composer:

```json
    "require": {
        "php": ">=5.4"
    },
```



#### Static call

```php
echo 

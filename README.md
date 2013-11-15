nano
=====

[![Build Status](https://travis-ci.org/Techworker/nano.png)](https://travis-ci.org/Techworker/nano)
[![Scrutinizer Quality Score](https://scrutinizer-ci.com/g/Techworker/nano/badges/quality-score.png?s=b61d7b3ff68cad9ede06c7574177b672458f80a9)](https://scrutinizer-ci.com/g/Techworker/nano/)
[![Code Coverage](https://scrutinizer-ci.com/g/Techworker/nano/badges/coverage.png?s=8f0b002ed08a392d9c7a8ea871edd367825be26a)](https://scrutinizer-ci.com/g/Techworker/nano/)
[![PHP >= 5.4](http://img.shields.io/php/%3E=5.4.png?color=red)]


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

### Installation

You have multiple ways to use the `Techworker\nano` class, but at first you have to install it. At best via composerby adding it the require list:

```json
{
    "require": {
        "techworker/nano": "dev-master"
    }
}
```

And installing the package:

```bash
composer install
```

After that you are ready to use `Techworker\Nano`, or you can install the package manually by downloading it (see download links) and installing it manually.

### Documentation

You have a lot of possibilities to use the `Techworker\Nano` class. We are going through some examples here but I'd like to invite you to the phpunit\tests directory to see all examples.

Lets start by explaining the replacement:

 - Each placeholder should be within curly brackets, like `{my_placeholder}`. 
 - You can access nested data by defining the access tree with placeholders divided by a colon, eg `{my_root_element:level1_element:level2_element}`. 
 - You can assign additional printf and sprintf formatting options, eg `{price|%.2f} &euro;`

To start, add the following `use` Statement to your code to access `Nano` directly or use the complete `\Techworker\Nano` namespace\class definition:

```php

echo Techworker\Nano::tpl("test");

use Techworker\Nano as Nano;
echo Nano::tpl("test");
```

#### Static call

The simplest and most non-intrusive method:

```php
echo Techworker\Nano::tpl("Agent {number}", array("number" => 7));
// outputs: Agent 7
```

#### Object-Oriented usage

Short and simple, some examples for the usage.

```php
echo (new Techworker\Nano("Agent {number}"))->data(array("number" => 7));
// outputs: Agent 7
echo (new Techworker\Nano("Agent {number|%03d}"))->data(array("number" => 7));
// outputs: Agent 007
```

##### Object Getter

```php
try{
    throw new \Exception("Exception Message", 110);
} catch(\Exception $ex) {
    echo new Techworker\Nano("Exception {message} and {code} thrown", $ex);
}
// outputs: Exception Exception Message and 110 thrown
```

##### Deep Structures

```php
echo new Techworker\Nano("Hello {name:firstname} {name:lastname}", [
    'name' => [
        'firstname' => 'Benjamin',
        'lastname' => 'Ansbach'
    ]
]);
// outputs: Hello Benjamin Ansbach
```

##### Simple values

```php
echo (new Techworker\Nano())->template("Hello {name}")->value("name", "Benjamin");
// outputs: Hello Benjamin
```

##### Default values
```php
echo (new Techworker\Nano())->template("Hello {name}")->default("Stranger");
// outputs: Hello Stranger
```

##### Complex and Nested!

```php
// Complex Example
$empOfTheMonth = new stdClass();
$empOfTheMonth->name = 'Benjamin Ansbach';
$empOfTheMonth->month = 2;
$empOfTheMonth->year = 2013;

class Income
{
    public function getAmount()
    {
        return 0;
    }
}

$data = [
    'company' => [
        'name' => 'Techworker',
        'employees' => ['Benjamin Ansbach', 'Techworker'],
        'income' => new Income(),
        'empofmonth' => $empOfTheMonth
    ]
];

$company = <<<EOT
Name: {company:name}
Employees: {company:employees:0} and {company:employees:1}
Income: {company:income:amount|%.2f}$
Employee of the Month: {company:empofmonth:name} in {company:empofmonth:year}/{company:empofmonth:month|%02d}
EOT;

echo (new Techworker\Nano($company, $data));

// outputs:
// Name: Techworker
// Employees: Benjamin Ansbach and Techworker
// Income: 0.00$
// Employee of the Month: Benjamin Ansbach in 2013/02

```

##### Instance Reusage Example

```php
$data = [
    ['firstname' => 'Benjamin', 'lastname' => 'Ansbach'],
    ['firstname' => 'The',      'lastname' => 'Techworker']
];

$nano = new Techworker\Nano("{firstname} {lastname}");
foreach($data as $item) {
    echo $nano->data($item) . "\n";
}

// outputs
// Benjamin Ansbach
// The Techworker
```

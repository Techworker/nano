nano
=====

Small and fast functionality for key => value oriented sprintf functionality, inspired by https://github.com/trix/nano.

### About

You know what I hate? String concats with $variables for simple messages like Exceptions:

    $message = "The Parameter " . $param . " with value " . $value . 
               " in method " . __CLASS__ . ":" . __FUNCTION__ . 
               " was simply wrong.";
    
To prevent this type of Coding-Horror I created `Techworker\Nano`. And to dive in directly, I show you the above code, the Nano way:

    $message = "The Parameter {param} with value {value} in method {class}:{method} was simply wrong";
    $message = \Techworker\Nano::tpl($message, [
        'param' => $param,
        'value' => $value,
        'method' => __METHOD__,
        'class' => __CLASS__
    ];

### More functionality

## Nested
<?php

require_once 'vendor/autoload.php';

$data = [
    ['firstname' => 'Benjamin', 'lastname' => 'Ansbach'],
    ['firstname' => 'The',      'lastname' => 'Techworker']
];

$nano = new Techworker\Nano("{firstname} {lastname}");
foreach($data as $item) {
    echo $nano->data($item) . "\n";
}

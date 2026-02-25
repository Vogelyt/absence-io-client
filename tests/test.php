<?php

require 'vendor/autoload.php';

use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\AbsenceClient;

$config = new Config(
    'YOUR_HAWK_ID',
    'YOUR_HAWK_KEY'
);

$client = new AbsenceClient($config);

print_r($client->users()->getAll());
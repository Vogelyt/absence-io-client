<?php

require 'vendor/autoload.php';

use Vogelyt\AbsenceIoClient\Config\Config;
use Vogelyt\AbsenceIoClient\AbsenceClient;

$config = new Config(
    '69ad40b79304bf861b628446',
    '78afae00ab767f3e5fdc07a3b16b8a31b346cf9727a4fea62cc70429d73f0110'
);

$client = new AbsenceClient($config);

print_r($client->users()->getAll());
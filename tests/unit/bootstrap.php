<?php

require __DIR__."/../../vendor/autoload.php";
require __DIR__."/functions.php";

# create mocks for the following namspaced functions
# this way we don't have to worry about them being called first
# see https://github.com/php-mock/php-mock#requirements-and-restrictions
$mockFunctions = [
    ["sndsgd\\http\\data\\decoder", "feof"],
    ["sndsgd\\http\\data\\decoder", "fread"],
    ["sndsgd\\http\\data\\decoder", "fwrite"],
    ["sndsgd\\http\\data\\decoder", "ini_get"],
];

foreach ($mockFunctions as list($namespace, $name)) {
    (new \phpmock\MockBuilder())
        ->setNamespace($namespace)
        ->setName($name)
        ->setFunction(function(){})
        ->build()
        ->define();
}

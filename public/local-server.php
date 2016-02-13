<?php

require __DIR__."/../vendor/autoload.php";


error_log($_SERVER["REQUEST_URI"]);

usleep(500000);

header("Content-Type: text/plain");
echo \sndsgd\Str::random(1024);

<?php

require __DIR__."/../vendor/autoload.php";

$timer = new \sndsgd\Timer("init request");
$request = new \sndsgd\http\inbound\Request($_SERVER);
$timer->stop();
$contentType = $request->getHeader('content-type');
$contentLength = (int) $request->getHeader('content-length');

if ($request->getMethod() === "POST") {
    $query = $_GET;
    $body = array_merge($_POST, $_FILES);
}
else {
    $timer = new \sndsgd\Timer("decode query");
    $query = $request->getQuery();
    $timer->stop();

    $timer = new \sndsgd\Timer("decode body");
    $body = \sndsgd\http\Data::decodeBody($contentType, $contentLength);
    $timer->stop();    
}





// print_R($_FILES);
// var_dump(microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"]);
// exit;




// print_r($_SERVER);

echo json_encode([
    // "ini" => ini_get_all(),
    "host" => $request->getHost(),
    "path" => $request->getPath(),
    "method" => $request->getMethod(),
    "query" => $query,
    "body" => $body,
    "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
    "memory" => \sndsgd\Fs::formatSize(memory_get_peak_usage()),
    "timers" => \sndsgd\Timer::getDurations(),
], \sndsgd\Json::HUMAN)."\n";
exit;

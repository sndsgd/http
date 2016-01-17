<?php

require __DIR__."/../vendor/autoload.php";

$request = new \sndsgd\http\inbound\Request($_SERVER);
$contentType = $request->getHeader('content-type');
$contentLength = (int) $request->getHeader('content-length');

echo json_encode([
    "path" => $request->getPath(),
    "method" => $request->getMethod(),
    "body" => \sndsgd\http\Data::decodeBody($contentType, $contentLength),
    "time" => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
], \sndsgd\Json::HUMAN)."\n";
exit;

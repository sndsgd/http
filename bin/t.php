<?php

require __DIR__ . "/../vendor/autoload.php";

/**
 * start the server
 * php -S localhost:8000 bin/t.php
 *
 * then try some requests
 *
 * http --form POST http://localhost:8000 value=1 some[nested][value]=123
 * http --form PATCH http://localhost:8000 value=1 some[nested][value]=123
 */

$durations = [];

$timer = new \sndsgd\Timer();
$request = new \sndsgd\http\Request($_SERVER);
$durations["init request object"] = $timer->stop();

$timer = new \sndsgd\Timer();
$query = $request->getQueryParameters();
$durations["decode query string"] = $timer->stop();

$timer = new \sndsgd\Timer();
$body = $request->getBodyParameters();
$durations["decode body"] = $timer->stop();

$timer = new \sndsgd\Timer(null, $_SERVER["REQUEST_TIME_FLOAT"]);
$durations["total"] = $timer->stop();

$responseBody = [
    "query" => $query,
    "body" => $body,
    "timers" => $durations,
];


$response = new \sndsgd\http\Response($request);
$response->setHeader("X-Awesome", "yes");
$response->setHeader("Content-Type", "application/json; charset=utf-8");
$response->setBody(json_encode($responseBody, 448));
$response->send();

// print_r($query);
// print_r($body);



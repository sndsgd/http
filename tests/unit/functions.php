<?php

function createTestEnvironment(array $server = [])
{
    return new \sndsgd\Environment(array_merge($server, [
        \sndsgd\Environment::SYSTEM_ENVVAR_NAME => \sndsgd\Environment::DEV,
    ]));
}

function createTestRequest(array $server = [])
{
    $environment = createTestEnvironment($server);
    return new \sndsgd\http\Request($environment);
}

function createTestClient(array $server = [])
{
    $request = createTestRequest($server);
    return new \sndsgd\http\request\Client($request);
}

function createTestHost(array $server = [])
{
    $request = createTestRequest($server);
    return new \sndsgd\http\request\Host($request);
}

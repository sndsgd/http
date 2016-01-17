<?php

require __DIR__."/../../vendor/autoload.php";

const HOST = "http://localhost:8000";

$dataDir = realpath(__DIR__."/../data");

if (php_sapi_name() === "cli") {
    var_dump("$dataDir/parameters.json");
    $parameters = file_get_contents("$dataDir/parameters.json");
    $parameters = json_decode($parameters, true);
    $files = glob("$dataDir/files/random-*");
    natsort($files);

    $curl = curl_init(HOST."/");
    curl_setopt_array($curl, [
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_USERAGENT => 'php-curl',
        CURLOPT_CUSTOMREQUEST => "PUT",
        CURLOPT_POSTFIELDS => createRequestParameters($parameters, $files),
    ]);
    $response = curl_exec($curl);
    $info = curl_getinfo($curl);
    echo $response;
    curl_close($curl);

    $args = [];

    $cmd = "http --form PUT ".HOST."/";
    foreach ($parameters as $name => $value) {
        $args[] = "'$name=$value'";
    }
    foreach ($files as $file) {
        $args[] = "'file@$file'";
    }

    shuffle($args);
    $args = implode(" ", $args);
    $cmd = "http --form PUT ".HOST."/ $args User-Agent:httpie";
    exec($cmd);
    exit(0);
}

elseif ($_SERVER["REQUEST_METHOD"] === "PUT") {
    $name = $_SERVER["HTTP_USER_AGENT"];
    if (preg_match("/[^a-z-]/i", $name)) {
        $name = substr(md5($name), 0, 8);
    }
    $path = "$dataDir/multipart/various/$name";
    file_put_contents("$path.type", $_SERVER["HTTP_CONTENT_TYPE"]);
    $contents = file_get_contents("php://input");
    //file_put_contents("$path.content", $contents);

    # update the file input names
    if ($name === "php-curl") {
        $regex = '/name="[0-9]"; file/';
        $contents = preg_replace($regex, 'name="file"; file', $contents);
    }
    file_put_contents("$path.content", $contents);

    
    echo "request written";
    exit(0);
}


function createRequestParameters($parameters, array $files = null)
{
    $ret = $parameters;
    foreach ($files as $file) {
        $ret[] = new CURLFile($file, "application/octet-stream", basename($file));
    }
    
    return $ret;
}



?>

<!doctype html>
<html>
<head>
    <title>multipart test data</title>
</head>
<body>
    <form method="post" enctype='multipart/form-data'>
        <input type="text" name="name">
        <input type="email" name="email">
        <input type="file" name="file">
        <input type="submit" name="submit" value="submit">
    </form>
</body>

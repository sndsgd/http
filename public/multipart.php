<?php

use \sndsgd\fs\File;

require __DIR__."/../vendor/autoload.php";

const BROWSER_NAME = "ie9";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dir = new Dir(__DIR__."/../tests/data/decoder/multipart-data");
    $dir->normalize();
    $file = $dir->getFile(BROWSER_NAME."-file.raw");
    echo $_SERVER["HTTP_CONTENT_TYPE"]."<br>";
    echo $file->getPath()."<br>";
    $contents = file_get_contents("php://input");
    var_dump($contents);
    $file->write($contents);
    exit;
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

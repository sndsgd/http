<?php

require __DIR__."/../../vendor/autoload.php";

const BYTES_PER_KB = 1024;
const BYTES_PER_MB = 1024 * 1024;

$dir = realpath(__DIR__."/../data/files");

$sizes = [
    BYTES_PER_KB,
    42 * BYTES_PER_KB,
    100 * BYTES_PER_KB - 1,
    100 * BYTES_PER_KB,
    100 * BYTES_PER_KB + 1,
    1 * BYTES_PER_MB,
    5 * BYTES_PER_MB,
];

foreach ($sizes as $totalBytes) {
    echo "$totalBytes\n";
    $path = "$dir/random-$totalBytes.dat";
    $fp = fopen($path, "w");

    $bytesRemaining = $totalBytes;
    do {
        echo "  $bytesRemaining\n";
        $bytesToWrite = ($bytesRemaining > 8192) ? 8192 : $bytesRemaining;
        $bytesWritten = fwrite($fp, \sndsgd\Str::random($bytesToWrite));
        $bytesRemaining -= $bytesWritten;
    }
    while ($bytesRemaining > 0);
    fclose($fp);
}

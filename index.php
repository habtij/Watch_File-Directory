<?php

use SebastianBergmann\CodeCoverage\Report\PHP;

$path = $argv[1];
$currentStatus = [];
readPath($path, $currentStatus);

while (true) {
    clearCache($path);
    checkPath($path);
    sleep(1);
}

function clearCache($path) {
    clearstatcache(false, $path);
}

function readPath($path, &$filesmap) {
    $filesmap[$path] = filemtime($path);
    
    if (is_dir($path)) {
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file == '.' || $file == '..') {
                continue;
            }
            $filename = $path ."/".$file;
            $filesmap[$filename] = filemtime($filename);
            
            if (is_dir($filename)) {
                readPath($filename, $filesmap);
            }
        }
    }
}

function checkPath($path) {
    global $currentStatus;

    $newstatus = [];
    readPath($path, $newstatus);
    foreach ($currentStatus as $file => $time) {
        if (!isset($newstatus[$file])) {
            echo "File \"$file\" was deleted...". PHP_EOL;
        } elseif ($newstatus[$file] !== $time) {
            echo "File \"$file\" was modified...". PHP_EOL;
        }
    }

    foreach ($newstatus as $file => $time) {
        if (!isset($currentStatus[$file])) {
            echo "File \"$file\" was added..." . PHP_EOL;
        }
    }

    $currentStatus = $newstatus;
}
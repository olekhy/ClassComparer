#!/usr/bin/env php
<?php
include_once __DIR__ . '/../../vendor/autoload.php';


use ClassComparer\Intersection\FilesIntersectLocator;
use ClassComparer\Intersection\ClassIntersectLocator;
use ClassComparer\Difference\MethodsLocator;
use ClassComparer\Scanner\DirectoryScanner;
use Zend\Stdlib\ArrayUtils;

/**
 * @todo fix MethodScanner to grab tokens correctly for empty abstract class method
 * @todo check before that method static declaration come after accessor declaration
 * @todo check that the accessor declaration present anyway
 */

$config = include 'config.php';

ini_set('memory_limit', $config['memory_limit']);
set_time_limit(0);
ini_set('display_errors', 1);
error_reporting($config['error_reporting']);


$directoryScanner = new DirectoryScanner;
$directoryScanner->setBlacklist($config['blacklist']);

$multiDirs = true;
foreach ($config['directories'] as $items) {
    $multiDirs = (bool) $multiDirs & ArrayUtils::isList($items);
}

$runnable = function($directories,  DirectoryScanner $directoryScanner) {
    $intersectFilesFinder = new FilesIntersectLocator(
        $directories,
        $directoryScanner
    );
    $methods = new MethodsLocator(new ClassIntersectLocator($intersectFilesFinder));
    echo join(PHP_EOL, $methods->getDifference());
};

try {

    if ($multiDirs) {
        foreach ($config['directories'] as $directories) {
            $runnable($directories, $directoryScanner);
        }
    } else {
            $runnable($config['directories'], $directoryScanner);
    }

} catch (Exception $e) {
    $t = $e->getTrace();
    $start = array_pop($t);
    error_log($e->getMessage() . PHP_EOL . $start['file'] . ':' . $start['line'] );
}
echo "memory used (MB): " . memory_get_peak_usage(true) / 1000 / 1000; // MB
echo PHP_EOL;










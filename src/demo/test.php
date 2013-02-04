#!/usr/bin/env php
<?php
include_once __DIR__ . '/../../vendor/autoload.php';

use ClassComparer\Intersection\FilesIntersectLocator;
use ClassComparer\Intersection\ClassIntersectLocator;
use ClassComparer\Difference\MethodsLocator;
use ClassComparer\Scanner\DirectoryScanner;

/**
 * @todo fix MethodScanner to grab tokens correctly for empty abstract class method
 * @todo check before that method static declaration come after accessor declaration
 * @todo check that the accessor declaration present anyway
 */

ini_set('memory_limit', '4G');
set_time_limit(-1);
ini_set('display_errors',1);
error_reporting(-1);

$directoryScanner = new DirectoryScanner;
$directoryScanner->setBlacklist(
    array(
    '.svn/',
    'alice/public',
    'bob/public',
    'bob/data',
    'conny/public',
    'conny/data',
    'log',
    'logs',
    'library/Apache',
    'library/PHPExcel',
    'library/Yii',
    'library/Zend',
    'unit/tests',
    'alice/vendor/tests'
));

try {
    $intersectFilesFinder = new FilesIntersectLocator(
        include 'config.php',
        $directoryScanner
    );
    $methods = new MethodsLocator(new ClassIntersectLocator($intersectFilesFinder));
    echo join(PHP_EOL, $methods->getDifference());
} catch (Exception $e) {
    $t = $e->getTrace();
    //var_dump(array_pop($t));
    $start = array_pop($t);
    error_log($e->getMessage() . PHP_EOL . $start['file'] . ':' . $start['line'] );
}
echo "memory used (MB): " . memory_get_peak_usage(true) / 1000 / 1000; // MB











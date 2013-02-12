#!/usr/bin/env php
<?php

set_include_path(implode(PATH_SEPARATOR, array(
    __DIR__ . '/vendor',
    __DIR__ . '/src',
    get_include_path()
)));
include_once __DIR__ . '/vendor/autoload.php';


use ClassComparer\Intersection\FilesIntersectLocator;
use ClassComparer\Printer;
use ClassComparer\Difference\Result\MethodsDifferenceResultInterface;
use ClassComparer\Intersection\ClassesIntersectLocator;
use ClassComparer\Difference\MethodSignsDifferenceLocator;
use ClassComparer\Scanner\DirectoryScanner;

/**
 * @todo fix MethodScanner to grab tokens correctly for empty abstract class method
 * @todo check before that method static declaration come after accessor declaration
 * @todo check that the accessor declaration present anyway
 */

$config = include 'src/config.php';

ini_set('memory_limit', $config['memory_limit']);
set_time_limit(0);
ini_set('display_errors', 1);
error_reporting((int)$config['error_reporting']);

$directoriesScanners = array();
foreach ($config['directories'] as $directory) {
        $directoryScanner = new DirectoryScanner;
        $directoryScanner->setBlacklist($config['blacklist']);
        $directoryScanner->addDirectory($directory);
        $directoriesScanners[] = $directoryScanner;
}

/**
 * @param array $directories
 * @param array $directoriesScanners
 * @param ClassComparer\Printer $printer
 *
 * @return int count of locate differences
 */
$runnable = function(array $directories, array $directoriesScanners, Printer $printer)
{
    $filesIntersectLocator = new FilesIntersectLocator($directories, $directoriesScanners);
    $count = 0;
    foreach ($filesIntersectLocator->getIntersection() as $files) {

        echo $printer->progress();

        $methods = new MethodSignsDifferenceLocator(new ClassesIntersectLocator($files));

        $diffResult = $methods->getDifference();
        $diffs = array();

        if (!empty($diffResult)) {
            /** @var $diffResult MethodsDifferenceResultInterface[] */
            foreach($diffResult as $result) {
                if ($result instanceof MethodsDifferenceResultInterface) {
                    foreach($result->getDiffsByPath() as $path => $diffsToPath) {
                        $diffs[$result->getClassName()][$path][] = $diffsToPath;
                    }
                }
            }

            foreach($diffs as $name => $diff) {
                $printer->addBlock($name, $diffs);
            }
            $count += count($diffs);
        }
    }
    return $count;
};

$printer = new Printer('Comparing signatures of class methods (Differences overview)', '');

$count = $runnable($config['directories'], $directoriesScanners, $printer);

$printer->unShitBlock('Number of differences: ' . $count, '');
$printer->setFooter(
    sprintf('(c) %s Rocket internet, memory used (MB): %s',
        '2013',
        (memory_get_peak_usage(true) / 1000 / 1000)
    )
);

sleep(1);
echo $printer;











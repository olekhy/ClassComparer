<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:40 PM
 */
namespace ClassComparer\Intersection;

use Exception;
use Zend\Code\Scanner\FileScanner;


class ClassesIntersectLocator implements IntersectAwareInterface
{
    /**
     * @var array
     */
    protected $entities;

    /**
     * @param $classFiles
     */
    public function __construct($classFiles)
    {
        $this->setEntities($classFiles);
    }

    /**
     * @param array $entities
     *
     * @return mixed
     */
    public function setEntities(array $entities)
    {
        $this->entities = $entities;
        return $this;
    }

    /**
     * @return array
     */
    public function getEntities()
    {
        return $this->entities;
    }

    /**
     *
     *
     * @return array
     */
    function getIntersection()
    {
        //echo 'Start computation of classes intersection' . PHP_EOL;
        $intersection = array();
        $classScanners = array();
        //echo memory_get_usage();
        $cachedClassNames = null;

        foreach ($this->getEntities() as $classFile) {

            $fileScanner = new FileScanner($classFile);

            try {
                $classNames = $fileScanner->getClassNames();

            } catch (Exception $e)
            {
                user_error(sprintf(
                    'Can not determine class name because file %s contains invalid class',
                    $classFile),
                    E_USER_NOTICE
                );
                continue;
            }

            if (count($classNames) > 1) {
                user_error(sprintf(
                    'Can not intersect classes because file %s contains more than one class',
                    $classFile),
                    E_USER_WARNING
                );
                $cachedClassNames = null;
                continue;
            }

            $className = array_shift($classNames);

            if ($cachedClassNames === $className || null === $cachedClassNames) {
                try {
                    $classScanner = $fileScanner->getClasses();
                    $classScanners[$classFile] = array_shift($classScanner);
                    $cachedClassNames = $className;
                } catch (Exception $e) {
                    $cachedClassNames = null;
                    continue;
                }
            }

        }
        if (!empty($classScanners)) {
            $intersection[] = $classScanners;
        }

        //echo PHP_EOL;
        //echo memory_get_usage();
        //echo 'Finish computation of classes intersection' . PHP_EOL;

        return $intersection;
    }
}

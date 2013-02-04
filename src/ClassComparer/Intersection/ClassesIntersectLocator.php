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


class ClassIntersectLocator implements IntersectAware
{

    /**
     * @var FilesIntersectLocator
     */
    protected $files;

    /**
     * @var array
     */
    protected $entities;

    /**
     * @param $filesScanner
     */
    public function __construct($filesScanner)
    {
        $this->setFiles($filesScanner);

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
        if (null === $this->entities) {
            $this->setEntities($this->getFiles()->getIntersection());
        }
        return $this->entities;
    }

    /**
     *
     *
     * @throws Exception\RangeException
     * @return array
     */
    function getIntersection()
    {
        echo 'Start computation of classes intersection' . PHP_EOL;
        $intersection = array();
        //echo memory_get_usage();
        foreach ($this->getEntities() as $classFiles) {

            $classScanners = array();
            $cachedClassNames = null;

            foreach ($classFiles as $classFile) {
                $fileScanner = new FileScanner($classFile);

                $classNames = array();
                try {
                    $classNames = $fileScanner->getClassNames();
                } catch (Exception $e)
                {
                    user_error(sprintf(
                        'Can not determine class name because file %s contains invalid class',
                        $classFile),
                        E_USER_NOTICE
                    );
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

                if ($cachedClassNames === $classNames || null === $cachedClassNames) {
                    try {
                        $classScanner = $fileScanner->getClasses();
                    } catch (Exception $e)
                    {
                        $cachedClassNames = null;
                        continue;
                    }
                }

                if (!empty($classScanner)) {
                    $classScanners[$classFile] = array_shift($classScanner);
                    $cachedClassNames = $classNames;
                }
            }
            if (!empty($classScanners)) {
                if (extension_loaded('igbinary')) {
                    $classScanners = igbinary_serialize($classScanners);
                }
                $intersection[] = $classScanners;
            }
        }

        //echo PHP_EOL;
        //echo memory_get_usage();
        echo 'Finish computation of classes intersection' . PHP_EOL;
        return $intersection;
    }

    /**
     * @param FilesIntersectLocator $files
     *
     * @return ClassIntersectLocator
     */
    public function setFiles(FilesIntersectLocator $files)
    {
        $this->files = $files;
        return $this;
    }

    /**
     * @return FilesIntersectLocator
     */
    public function getFiles()
    {
        return $this->files;
    }
}

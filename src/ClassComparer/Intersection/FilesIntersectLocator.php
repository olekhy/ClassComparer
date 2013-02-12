<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:28 PM
 */
namespace ClassComparer\Intersection;

use ClassComparer\Scanner\DirectoryScanner;

class FilesIntersectLocator implements IntersectAwareInterface
{
    /**
     * @var array paths to directories to scan
     */
    protected $entities;

    /**
     * @var DirectoryScanner[]
     */
    protected $scanners;

    /**
     * @param array $directories
     * @param $scanners
     *
     * @internal param DirectoryScanner $scanner
     */
    public function __construct(array $directories, $scanners)
    {
        $this->setDirectoriesToScan($directories);
        $this->setScanners($scanners);

    }

    /**
     *
     * @return array
     */
    public function getIntersection()
    {
        $arrays = array();
        $intersection = array();

        foreach ($this->getScanners() as $scanner) {
            $arrays[] = $scanner->getFilesRel();
        }

        $relativePathsIntersected = call_user_func_array('array_intersect', $arrays);

        foreach ($relativePathsIntersected as $path) {
            $res = array();
            foreach ($this->getEntities() as $entity) {
                 $res[] = $entity . $path;
            }
            $intersection[] = $res;
        }

        return $intersection;
    }

    /**
     * @param array $directories
     *
     * @return FilesIntersectLocator
     * @throws Exception\InvalidArgumentException
     */
    public function setEntities(array $directories)
    {
        $error = false;
        if (!empty($directories)) {

            foreach ($directories as $path) {

                if (!is_dir($path))  {
                    $error = true;
                    break;
                }
            }
        }

        if ($error || empty($directories) || count($directories) < 2) {
            throw new Exception\InvalidArgumentException('Invalid directory were provided');
        }
        $this->entities = $directories;
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
     * @param array $directories
     *
     *
     * @return FilesIntersectLocator
     */
    public function setDirectoriesToScan(array $directories)
    {
        $this->setEntities($directories);
        return $this;
    }

    /**
     *
     * @return DirectoryScanner[]
     */
    public function getScanners()
    {
        return $this->scanners;
    }

    /**
     * @param DirectoryScanner[] $scanners
     *
     * @return FilesIntersectLocator
     */
    protected function setScanners(array $scanners)
    {
        $this->scanners = $scanners;
        return $this;
    }

}

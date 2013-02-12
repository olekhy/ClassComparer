<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:59 PM
 */
namespace ClassComparer\Difference\Result;

class DifferenceResult implements ResultInterface
{
    protected $className;
    protected $paths;
    protected $diff;

    public function __construct($className, $diff, $paths )
    {
        $this->setDiff($diff);
        $this->setClassName($className);
        $this->setPaths($paths);
    }

    public function setClassName($className)
    {
        $this->className = $className;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setDiff(array $methodsDiff)
    {
        $this->diff = $methodsDiff;
    }

    public function getDiff()
    {
        return $this->diff;
    }

    public function setPaths($paths)
    {
        $this->paths = $paths;
    }

    public function getPaths()
    {
        return $this->paths;
    }

    public function getDiffsByPath()
    {
        return array_combine(array_values($this->getPaths()), $this->getDiff());
    }

    public function __toString()
    {
        return '';

    }
}

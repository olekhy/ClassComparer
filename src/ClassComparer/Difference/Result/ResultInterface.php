<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/5/13
 *         Time: 11:04 PM
 */
namespace ClassComparer\Difference\Result;

interface ResultInterface
{
    public function setClassName($className);

    public function getClassName();

    public function setDiff(array $methodsDiff);

    public function getDiff();

    public function setPaths($paths);

    public function getPaths();


    /**
     * @return array
     */
    public function getDiffsByPath();

}

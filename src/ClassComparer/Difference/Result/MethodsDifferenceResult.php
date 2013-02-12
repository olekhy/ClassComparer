<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:59 PM
 */
namespace ClassComparer\Difference\Result;

class MethodsDifferenceResult extends DifferenceResult implements
    MethodsDifferenceResultInterface
{
    protected $methodName;

    public function __construct($className, $method, $diff, $paths )
    {
        parent::__construct($className, $diff, $paths);
        $this->setMethodName($method);
    }

    public function setMethodName($methodName)
    {
        $this->methodName = $methodName;
    }

    public function getMethodName()
    {
        return $this->methodName;
    }

    public function getMethodNameFull()
    {
        return sprintf('%s::%s', $this->getClassName(), $this->getMethodName());
    }


}

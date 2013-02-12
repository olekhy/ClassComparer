<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/5/13
 *         Time: 11:04 PM
 */
namespace ClassComparer\Difference\Result;

interface MethodsDifferenceResultInterface extends ResultInterface
{

    public function setMethodName($methodName);

    public function getMethodName();

    public function getMethodNameFull();

}

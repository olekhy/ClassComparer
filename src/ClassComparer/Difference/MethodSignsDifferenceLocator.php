<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/3/13
 *         Time: 12:01 AM
 */
namespace ClassComparer\Difference;


use ClassComparer\Intersection\ClassesIntersectLocator;
use ClassComparer\Difference\Result\MethodsDifferenceResultInterface;
use Zend\Code\Scanner\MethodScanner;
use Zend\Code\Scanner\ClassScanner;
use Zend\Code\Scanner\ParameterScanner;


class MethodSignsDifferenceLocator
{
    const MODIFIERS = 'modifiers';
    const PARAMS = 'params';
    const SIGNATURE = 'signature';

    /**
     * @var ClassIntersectLocator
     */

    protected $classesIntersect;

    /**
     * @var string  difference result object class name
     */
    protected $classNameDiffResultObject;

    /**
     * @param $classesIntersect
     * @param string $resultClassName
     *
     * @throws Exception\InvalidArgumentException
     */
    public function __construct(ClassesIntersectLocator $classesIntersect,
                                $resultClassName = 'ClassComparer\\Difference\\Result\\MethodsDifferenceResult'
    ) {
        if (!class_exists($resultClassName)) {
            throw new Exception\InvalidArgumentException(
                sprintf('Invalid result class name %s', $resultClassName)
            );
        }

        $this->classNameDiffResultObject = $resultClassName;
        $this->setClassesIntersect($classesIntersect);
    }

    /**
     * @return array
     */
    function getDifference()
    {
        $result = array();

        $classes = $this->getClassesIntersect()->getIntersection();
        foreach ($classes as $classScanners) {

            $methods = $this->getMethodNamesIntersect($classScanners);

            if (empty($methods)) {
                continue;
            }

            $info = array();
            $info2 = array();

            /** @var $classScanners ClassScanner[] */
            foreach ($classScanners as $fileName => $classScanner) {
                $className = $classScanner->getName();

                $info2[$className][] = $fileName;

                foreach ($methods as $methodName) {
                    try {
                        $methodScanner = $classScanner->getMethod($methodName);


                        $modifiers = $this->getMethodModifiersInfo($methodScanner);
                        $parameters = $this->getMethodParamsInfo($methodScanner);
                        $info[$className][$methodName][static::SIGNATURE][] =
                            array(sprintf('%s%s%s', $modifiers, $methodName, $parameters));

                    }  catch (\Exception $e) {

                    }
                }
            }

            $result = array_merge($result, $this->getMethodsDifference($info, $info2));
        }

        return $result;
    }

    /**
     * @param MethodScanner $methodScanner
     *
     * @return array
     */
    protected function getMethodParamsInfo(MethodScanner $methodScanner)
    {
        $parameters = array();
        $paramScanners = $methodScanner->getParameters(true);
        /** @var $paramScanners ParameterScanner[] */
        foreach ($paramScanners as $paramScanner) {
            $index = $paramScanner->getPosition();
            $type = $paramScanner->isArray() ? 'array' : $paramScanner->getClass();
            $type = $type ? $type . ' ' : '';
            $name = ($paramScanner->isPassedByReference() ? '&' : '') . '$' . $paramScanner->getName();
            $value = '';
            if ($paramScanner->isOptional()) {
                $value = ' = ' . trim((
                $paramScanner->isDefaultValueAvailable()
                    ? $paramScanner->getDefaultValue()
                    : ''
                ));
            }
            $value = preg_replace('/\s+/', ' ', $value);
            $parameters[$index] = sprintf('%s%s%s', $type, $name, $value);
        }
        asort($parameters);
        return '(' . join(', ', $parameters) . ')';
    }

    /**
     * @param ClassScanner[] $classScanners
     *
     * @return array
     */
    protected function getMethodNamesIntersect(array $classScanners)
    {
        $names = null;
        /** @var $classScanners ClassScanner[] */
        foreach ($classScanners as $classScanner) {
            if (null !== $classScanner) {
                $names[] = $classScanner->getMethodNames();
            }
        }

        return call_user_func_array('array_intersect', $names);
    }

    /**
     * @param MethodScanner $methodScanner
     *
     * @return array
     */
    public function getMethodModifiersInfo(MethodScanner $methodScanner)
    {
        switch (true) {
            case $methodScanner->isPrivate() :
                $accessor = 'private';
                break;
            case $methodScanner->isProtected() :
                $accessor = 'protected';
                break;
            case $methodScanner->isPublic() :
                $accessor = 'public';
                break;
            default :
                $accessor = '';
        }

        return sprintf('%s%s%s%s%s',
            $methodScanner->isFinal() ? 'final ' : '',
            $methodScanner->isAbstract() ? 'abstract ' : '',
            $accessor . ' ',
            $methodScanner->isStatic() ? 'static ' : '',
            'function '
        );
    }

    /**
     * @param array $info
     * @param array $info2
     *
     * @return array
     */
    protected function getMethodsDifference(array $info, array $info2)
    {
        $difference = array();
        foreach ($info2 as $className => $classPaths) {

            if (isset($info[$className])) {

                foreach ($info[$className] as $methodName => $methodInfo) {

                    $methodsDiff = $this->getDiff($methodInfo, static::SIGNATURE);

                    if ($this->hasDifferences($methodsDiff)) {
                        /** @var $resultClass MethodsDifferenceResultInterface*/
                        $resultClass = $this->getClassNameDiffResultObject();
                        $difference[] = new $resultClass(
                            $className, $methodName, $methodsDiff, $classPaths
                        );
                        //$difference[$className][$methodName]['diff'] = $methodsDiff;
                        //$difference[$className][$methodName]['paths'] = $classPaths;
                    }
                }
            }
            break;
        }

        return $difference;
    }

    public function getClassNameDiffResultObject()
    {
        return $this->classNameDiffResultObject;
    }

    /**
     * @param $methodInfo
     * @param $scope
     *
     * @return array
     */
    protected function getDiff(array $methodInfo, $scope)
    {
        $result = array();

        if (isset($methodInfo[$scope])) {
            $result = array(
                call_user_func_array('array_diff', $methodInfo[$scope]),
                call_user_func_array('array_diff', array_reverse($methodInfo[$scope]))
            );
        }

        return $result;
    }

    /**
     * @return ClassIntersectLocator
     */
    public function getClassesIntersect()
    {
        return $this->classesIntersect;
    }

    /**
     * @param $classesIntersect
     *
     * @return MethodSignsDifferenceLocator
     */
    public function setClassesIntersect($classesIntersect)
    {
        $this->classesIntersect = $classesIntersect;
        return $this;
    }

    /**
     * @param array $methodsDiff
     *
     * @return bool
     */
    private function hasDifferences(array $methodsDiff)
    {
        $hasDifference = false;


        foreach ($methodsDiff as $diff) {
            if (!empty($diff)) {
                $hasDifference = true;
            }
        }
        return $hasDifference;
    }
}

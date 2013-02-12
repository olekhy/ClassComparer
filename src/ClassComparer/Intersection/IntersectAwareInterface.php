<?php
/**
 * Created by JetBrains PhpStorm.
 * @author Oleksandr Khutoretskyy <olekhy@gmail.com>
 *         Date: 2/2/13
 *         Time: 11:25 PM
 */
namespace ClassComparer\Intersection;

interface IntersectAwareInterface
{

    /**
     * @param array $entities
     *
     * @return mixed
     */
    public function setEntities(array $entities);

    /**
     * @return array
     */
    public function getEntities();

    /**
     * @return array
     */
    function getIntersection();

}

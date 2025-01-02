<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Service;

/**
 * Allows dependency injection of classes used statically
 * Usage:
 * $injectable = new InjectableStaticService();
 * $injectable->setClass(SomeClass::class);
 * $injectable->someStaticMethodOfSomeClass($parameter1, $parameter2, ...);
 */
class InjectableStaticService
{
    private string $class;

    public function setClass(string $class) : void
    {
        $this->class = $class;
    }

    public function __call($method, array $parameters)
    {
        return call_user_func([$this->class, $method], ...$parameters);
    }
}
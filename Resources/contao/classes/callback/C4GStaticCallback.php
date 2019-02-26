<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    6
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Resources\contao\classes\callback;


/**
 * Class C4GStaticCallback
 * C4GObjectCallback is generally preferable over this. Only use this if absolutely necessary.
 * @package con4gis\CoreBundle\Resources\contao\classes\callback
 */
class C4GStaticCallback extends C4GCallback
{
    protected $class;       //Class on which the method will be called.
    protected $method;      //Method to be called.

    /**
     * C4GStaticCallback constructor.
     * @param string $class
     * @param string $method
     */
    public function __construct(string $class, string $method) {
        $this->class = $class;
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function call() {
        $parameters = func_get_args();
        $numParameters = func_num_args();
        $class = $this->class;
        $method = $this->method;
        if ($numParameters === 0) {
            return $class::$method();
        } else {
            return call_user_func_array(array($class, $method), $parameters);
        }
    }
}
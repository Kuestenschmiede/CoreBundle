<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Resources\contao\classes\callback;


class C4GObjectCallback extends C4GCallback
{
    protected $object;      //Object on which the method will be called.
    protected $method;      //Method to be called.

    /**
     * C4GObjectCallback constructor.
     * @param $object
     * @param string $method
     */
    public function __construct($object, string $method) {
        if (is_object($object)) {
            $this->object = $object;
        }
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function call() {
        $parameters = func_get_args();
        $numParameters = func_num_args();
        $object = $this->object;
        $method = $this->method;
        if ($numParameters === 0) {
            return $object->$method();
        } else {
            return call_user_func_array(array($object, $method), $parameters);
        }
    }
}
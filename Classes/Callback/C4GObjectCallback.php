<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Resources\Callback;

class C4GObjectCallback extends C4GCallback
{
    protected $object;      //Object on which the method will be called.
    protected $method;      //Method to be called.

    /**
     * C4GObjectCallback constructor.
     * @param $object
     * @param string $method
     */
    public function __construct($object, string $method)
    {
        if (is_object($object)) {
            $this->object = $object;
        }
        $this->method = $method;
    }

    /**
     * @return mixed
     */
    public function call()
    {
        $parameters = func_get_args();
        $numParameters = func_num_args();
        $object = $this->object;
        $method = $this->method;
        if ($numParameters === 0) {
            return $object->$method();
        }

        return call_user_func_array([$object, $method], $parameters);
    }
}

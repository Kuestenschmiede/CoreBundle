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


class C4GAnonymousCallback extends C4GCallback
{
    protected $function;      //Function to be called.

    /**
     * C4GAnonymousCallback constructor.
     * @param \Closure $function
     */
    public function __construct(\Closure $function) {
        $this->function = $function;
    }

    /**
     * @return mixed
     */
    public function call() {
        $parameters = func_get_args();
        $numParameters = func_num_args();
        $function = $this->function;
        if ($numParameters === 0) {
            return $function();
        } else {
            return call_user_func_array($function, $parameters);
        }
    }
}
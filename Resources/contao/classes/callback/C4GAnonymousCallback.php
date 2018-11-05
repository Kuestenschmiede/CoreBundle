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
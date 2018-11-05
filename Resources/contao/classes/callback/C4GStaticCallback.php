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
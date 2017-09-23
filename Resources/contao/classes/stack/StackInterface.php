<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2017.
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Resources\contao\classes\stack;

/**
 * Interface StackInterface
 * @package c4g\projects
 */
interface StackInterface
{


    /**
     * add an item to the top of the stack
     * @param array $item
     * @return mixed
     */
    public function push(array $item);


    /**
     * remove the last item added to the top of the stack
     * @return mixed
     */
    public function pop();


    /**
     * look at the item on the top of the stack without removing it
     * @return mixed
     */
    public function top();


    /**
     * return whether the stack contains no more items
     * @return mixed
     */
    public function isEmpty();
}

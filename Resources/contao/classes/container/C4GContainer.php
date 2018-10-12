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

namespace con4gis\CoreBundle\Resources\contao\classes\container;


class C4GContainer extends C4GBaseContainer
{
    public function addElement($element, $key = null) {
        if (is_object($element) || is_array($element)) {
            throw new \Exception('C4GContainer instances may not take objects or arrays as elements.');
        }
        return $this->add($element, $key);
    }

    public function deleteElement($key) {
        return $this->delete($key);
    }

    public function addElementsFromArray(array $array) {
        foreach ($array as $key => $value) {
            $this->addElement($value, $key);
        }
    }
}
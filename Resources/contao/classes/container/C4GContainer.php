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
        if (is_array($element)) {
            throw new \Exception('C4GContainer instances may not take arrays or objects other than C4GContainer instances as elements.');
        } elseif (is_object($element)) {
            if (!$element instanceof self) {
                throw new \Exception('C4GContainer instances may not take arrays or objects other than C4GContainer instances as elements.');
            } elseif ($element === $this) {
                throw new \Exception('C4GContainer instances may not take themselves as elements.');
            }
        }
        return $this->add($element, $key);
    }

    public function deleteElement($key) {
        return $this->delete($key);
    }

    /**
     * Add a one-dimensional array to the container, where every array element becomes a container element.
     * @param array $array
     * @throws \Exception
     */
    public function addElementsFromArray(array $array) {
        foreach ($array as $key => $value) {
            $this->addElement($value);
        }
    }

    /**
     * Add a two-dimensional array to the container, where every outer array element becomes a container
     *  and every inner array element becomes an element of the corresponding container.
     * @param array $array
     * @throws \Exception
     */
    public function addContainersFromArray(array $array) {
        foreach ($array as $value) {
            $container = new C4GContainer();
            foreach ($value as $k => $v) {
                $container->addElement($v, $k);
            }
            $this->addElement($container);
        }
    }

    public function hasSameContentAs(C4GContainer $container) {
        foreach ($container as $key => $value) {
            if ($this->containsKey($key) === false) {
                return false;
            }
            if ($value instanceof C4GContainer) {
                if ($value->hasSameContentAs($this->getByKey($key)) === false) {
                    return false;
                }
            } else {
                if ($value !== $this->getByKey($key)) {
                    return false;
                }
            }
        }
        foreach ($this as $key => $value) {
            if ($container->containsKey($key) === false) {
                return false;
            }
        }
        return true;
    }
}
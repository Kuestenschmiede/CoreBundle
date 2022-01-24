<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Container;

abstract class C4GBaseContainer implements \Iterator, \Countable
{
    protected $elements = [];
    protected $keys = [];
    protected $current = 0;

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return $this->elements[$this->current];
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->current += 1;
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return $this->keys[$this->current];
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return isset($this->elements[$this->current]);
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->current = 0;
    }

    final protected function add($element, $key = null)
    {
        $this->elements[] = $element;
        if ($key === null || (!is_string($key) && !is_int($key))) {
            $key = is_countable($this->elements) ? count($this->elements) - 1 : 0;
        }
        $this->keys[] = $key;

        return $key;
    }

    final protected function delete($key)
    {
        $index = array_search($key, $this->keys);
        if ($index !== false) {
            $this->keys = array_splice($this->keys, $index);
            $this->elements = array_splice($this->elements, $index);

            return true;
        }

        return false;
    }

    public function getByKey($key)
    {
        $index = array_search($key, $this->keys);
        if ($index !== false) {
            return $this->elements[$index];
        }

        return null;
    }

    public function containsKey($key)
    {
        return array_search($key, $this->keys) === false ? false : true;
    }

    public function clear()
    {
        $this->elements = [];
        $this->keys = [];
        $this->current = 0;
    }

    public function isEmpty()
    {
        return empty($this->elements);
    }

    public function count()
    {
        return is_countable($this->keys) ? count($this->keys) : 0;
    }
}

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
namespace con4gis\CoreBundle\Classes\Stack;

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

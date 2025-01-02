<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Helper;

class ArrayHelper
{
    /**
     * For HTML SELECTBOX
     * @param $array
     * @param $on
     * @param int $order
     * @return array
     */
    public static function array_sort($array, $on, $order = SORT_ASC, $newKeys = false)
    {
        $new_array = [];
        $sortable_array = [];

        if (is_countable($array) && (count($array) > 0)) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);

                    break;
                case SORT_DESC:
                    arsort($sortable_array);

                    break;
            }

            foreach ($sortable_array as $k => $v) {
                if ($newKeys) {
                    $new_array[] = $array[$k];
                } else {
                    $new_array[$k] = $array[$k];
                }
            }
        }

        return $new_array;
    }

    /**
     * For Object collections
     * @param $array
     * @param $on
     * @param int $order
     * @return array
     */
    public static function array_collection_sort($array, $on, $order = SORT_ASC, $newKeys = false, $rowCount = 0)
    {
        $new_array = [];
        $sortable_array = [];

        if (is_countable($array) && (count($array) > 0)) {
            foreach ($array as $k => $v) {
                $value = $v->$on;
                if ($value) {
                    $sortable_array[$k] = $value;
                }
            }

            switch ($order) {
                case SORT_ASC:
                case 'asc':
                    asort($sortable_array);

                    break;
                case SORT_DESC:
                case 'desc':
                    arsort($sortable_array);

                    break;
            }

            $cnt = 0;
            foreach ($sortable_array as $k => $v) {
                if ($newKeys) {
                    $new_array[] = $array[$k];
                } else {
                    $new_array[$k] = $array[$k];
                }
                $cnt++;
                if (($rowCount > 0) && ($cnt >= $rowCount)) {
                    break;
                }
            }
        }

        return $new_array;
    }

    /**
     * @param $arr
     * @param $fields
     * @return mixed
     */
    public static function sortArrayByFields($arr, $fields)
    {
        $sortFields = [];
        $args = [];

        if (!$fields) {
            return $arr;
        }

        foreach ($arr as $key => $row) {
            if (is_array($fields)) {
                foreach ($fields as $field => $order) {
                    $sortFields[$field][$key] = strtolower($row[$field]);
                }
            } else {
                $sortFields[$field][$key] = strtolower($fields);
            }
        }

        if (is_array($fields)) {
            foreach ($fields as $field => $order) {
                $args[] = $sortFields[$field];

                if (is_array($order)) {
                    foreach ($order as $pt) {
                        $args[] = $pt;
                    }
                } else {
                    $args[] = $order;
                }
            }
        } else {
            $args[] = $fields;
            $args[] = SORT_ASC;
        }

        $args[] = &$arr;

        call_user_func_array('array_multisort', $args);

        return $arr;
    }

    /**
     * @param $array
     * @param $index
     * @param $value
     * @return mixed
     */
    public static function filter_by_value($array, $index, $value, $newKeys = false)
    {
        if (is_array($array) && is_countable($array) && (count($array) > 0)) {
            $newarray = [];
            foreach (array_keys($array) as $key) {
                $temp[$key] = $array[$key][$index];

                if ($temp[$key] == $value) {
                    if ($newKeys) {
                        $newarray[] = $array[$key];
                    } else {
                        $newarray[$key] = $array[$key];
                    }
                }
            }
        }

        return $newarray;
    }

    /**
     * @param $array
     * @return bool|\stdClass
     */
    public static function arrayToObject($array)
    {
        if (!is_array($array)) {
            return $array;
        }
        $object = new \stdClass();
        if (is_array($array) && is_countable($array) && (count($array) > 0)) {
            foreach ($array as $name => $value) {
                $name = (trim($name));
                if ($name != '') {
                    $object->$name = static::arrayToObject($value);
                }
            }

            return $object;
        }

        return false;
    }

    /**
     * @param $object
     * @return array|mixed
     */
    public static function objectToArray($object) {
        if (is_object($object))
            $d = get_object_vars($object);

        return is_array($object) ? array_map(__FUNCTION__, $object) : $object;
    }

}

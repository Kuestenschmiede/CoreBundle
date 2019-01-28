<?php
/**
 * con4gis
 * @version   php 7
 * @package   con4gis
 * @author    con4gis authors (see "authors.txt")
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Entity;

/**
 * Class BaseEntity
 * @package con4gis\CoreBundle\Entity
 */
abstract class BaseEntity
{

    /**
     * BaseEntiy constructor.
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->setData($data);
    }


    /**
     * Setzt die Daten eines Arrays als Eigenschaften der Klasse.
     * @param $data
     */
    public function setData($data)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $column => $value) {
                if (property_exists($this, $column)) {
                    $column = 'set' . ucfirst($column);
                    $this->$column($value);
                }
            }
        }
    }

    public function toArray()
    {
        return get_object_vars($this);
    }
}

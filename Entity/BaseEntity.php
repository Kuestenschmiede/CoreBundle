<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\CoreBundle\Entity;

/**
 * Class BaseEntity
 * @package con4gis\CoreBundle\Entity
 */
abstract class BaseEntity
{

    /**
     * BaseEntity constructor.
     * @param array $data
     */
    public function __construct($data = array())
    {
        $this->setData($data);
    }


    /**
     * Setzt die Daten eines Arrays als Eigenschaften der Klasse.
     *  ¿Por qué no inglés? ¡No entendo alemán!
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

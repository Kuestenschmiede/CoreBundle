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
namespace con4gis\CoreBundle\Classes\Stack;

/**
 * Class StackDatabase
 * @package c4g\core
 */
class StackDatabase implements StackInterface
{
    /**
     * Name der Tabelle, in der der Stack gespeichert wird.
     * (Wird in der Kindklasse gesetzt!)
     * @var string
     */
    protected $table = '';

    /**
     * Gibt die Datenbanklasse zurück.
     * @return \Contao\Database
     */
    protected function getDb()
    {
        return \Contao\Database::getInstance();
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Führt eine Datanbankabfrage aus.
     * (Kapselt den Zugriff auf \Contao\Database)
     * @param $query
     */
    public function execute($query)
    {
        $db = $this->getDb();

        return $db->execute($query);
    }

    /**
     * Prüft, ob ein Feld existiert.
     * (Kapselt den Zugirff auf \Contao\Database)
     * @param $field
     * @return bool
     */
    public function fieldExists($field)
    {
        $db = $this->getDb();

        return $db->fieldExists($field, $this->table);
    }

    /**
     * Fügt dem Stack ein Element hinzu.
     * @param $item
     */
    public function push(array $item)
    {
        if (count($item)) {
            $data = serialize($item);
            $query = 'INSERT INTO `' . $this->table . '` SET ';
            $query .= '`data` = \'' . $data . '\', ';
            $query .= ' `tstamp` = ' . time();
            $this->execute($query);
        }
    }

    /**
     * Entfernt ein Element vom Stack.
     * @return array|mixed
     */
    public function pop()
    {
        $data = $this->top();

        if (is_array($data) && count($data)) {
            $query = 'DELETE FROM `' . $this->table . '` WHERE `id` = ' . $data['id'];
            $this->execute($query);

            return $data;
        }

        return [];
    }

    /**
     * Gibt das oberste Element vom Stack zurück.
     * @return array
     */
    public function top()
    {
        $query = 'SELECT * FROM `' . $this->table . '` ORDER BY id ASC LIMIT 1';
        $result = $this->execute($query);

        if ($result->numRows) {
            return $result->fetchAssoc();
        }

        return [];
    }

    /**
     * Prüft, ob der Stack leer ist.
     * @return bool
     */
    public function isEmpty()
    {
        $query = 'SELECT * FROM `' . $this->table . '` ORDER BY id ASC LIMIT 1';
        $result = $this->execute($query);

        return ($result->numRows) ? false : true;
    }
}

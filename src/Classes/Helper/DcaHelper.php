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

use con4gis\CoreBundle\Classes\C4GUtils;
use con4gis\MapsBundle\Classes\Utils;
use con4gis\ProjectsBundle\Classes\Common\C4GBrickCommon;
use Contao\Database;
use Contao\Image;
use Contao\Input;
use Contao\System;

use Contao\Versions;

/**
 * Class DcaHelper
 * @package con4gis\CoreBundle\Classes\Helper
 */
class DcaHelper
{
    /**
     * save_callback: Erstelle eine Uuid.
     * @param $varValue
     * @param $dc
     * @return string
     */
    public static function generateUuid($varValue, $dc)
    {
        if (!$varValue) {
            return C4GBrickCommon::getGUID();
        }

        return $varValue;
    }

    /**
     * @param $dc
     * @return array
     */
    public function cbGetTables($dc)
    {
        $db = Database::getInstance();
        $tables = $db->listTables();
        if ($dc->activeRecord->saveTlTables !== '1') {
            foreach ($tables as $key => $value) {
                if (C4GUtils::startsWith($value, 'tl_') === true) {
                    unset($tables[$key]);
                }
            }
        }
        $options = [];
        foreach ($tables as $table) {
            $options[$table] = $table;
        }

        return $options;
    }

    /**
     * options_callback: Gibt eine Liste der Felder einer Tabelle zurück.
     * @param $dc
     * @return array
     */
    public function cbGetFields($dc, $srcTable = '')
    {
        $data = [];
        if ($srcTable !== '') {
            $table = $srcTable;
        } else {
            $table = $dc->activeRecord->srctable;
        }
        $db = Database::getInstance();

        if ($table) {
            System::loadLanguageFile($table);
            $fields = $db->listFields($table);

            if (is_array($fields) && count($fields)) {
                foreach ($fields as $field) {
                    if ($field['name'] != 'PRIMARY') {
                        if (isset($GLOBALS['TL_LANG'][$table][$field['name']][0])) {
                            $label = $GLOBALS['TL_LANG'][$table][$field['name']][0];
                            $label .= ' [' . $field['name'] . ' (' . $table . ')]';
                            $data[$field['name']] = $label;
                        } else {
                            $data[$field['name']] = $field['name'];
                        }
                    }
                }
            }
        }

        return $data;
    }

    /**
     * @param $varValue
     * @param $dc
     * @return mixed
     * @throws \Exception
     */
    public function setLocLon($varValue, $dc)
    {
        if (!Utils::validateLon($varValue)) {
            throw new \Exception($GLOBALS['TL_LANG']['c4g_maps']['geox_invalid']);
        }

        return $varValue;
    }

    /**
     * @param $varValue
     * @param $dc
     * @return mixed
     * @throws \Exception
     */
    public function setLocLat($varValue, $dc)
    {
        if (!Utils::validateLat($varValue)) {
            throw new \Exception($GLOBALS['TL_LANG']['c4g_maps']['geoy_invalid']);
        }

        return $varValue;
    }

    /**
     * Gibt einen HTML-String zurück, der den Geopicker enthält.
     * @param \DataContainer $dc
     * @return string
     */
    public function getPickerLink(\Contao\DataContainer $dc)
    {
        if (!$GLOBALS['TL_JAVASCRIPT']['c4g-maps-backend']) {
            $GLOBALS['TL_JAVASCRIPT']['c4g-maps-backend'] = 'bundles/con4gismaps/js/c4g-maps-backend.js';
        }
        $input = Input::get('act');
        $strField = 'ctrl_' . $dc->field . (($input == 'editAll') ? '_' . $dc->id : '');
        if (substr($strField, -1, 1) == 'y') {
            $strFieldX = substr($strField, 0, -1) . 'x';
            $strFieldY = $strField;
        } else {
            $strFieldX = $strField;
            $strFieldY = substr($strField, 0, -1) . 'y';
        }
        $requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();
        return ' <a href="con4gis/api/geopickerService?rt=' . $requestToken .
            '" title="' . $GLOBALS['TL_LANG']['c4g_maps']['geopicker'] .
            '" style="padding-left:3px" onclick="c4g.maps.backend.showGeoPicker(this.href,' .
            $strFieldX . ',' . $strFieldY . ', {title:\'' . $GLOBALS['TL_LANG']['c4g_maps']['geopicker'] . '\'});return false">' .
            Image::getHtml('bundles/con4gismaps/js/images/be-icons/geopicker.png',
                $GLOBALS['TL_LANG']['tl_content']['editalias'][0], 'style="vertical-align:top"') . '</a>';
    }

    /**
     * Springt in die Übersicht zurück.
     * @param $href
     * @param $label
     * @param $title
     * @param $class
     * @param $attributes
     * @return string
     */
    public function back($href, $label, $title, $class, $attributes)
    {
        $rt = Input::get('rt');
        if (!$rt) {
            $do = 'c4g_bricks';
        } else {
            $do = $rt;
        }

        $href = System::getContainer()->get('router')->generate('contao_backend'). '?do=' . $do/* . "&rt=$rt"*/;

        return '<a href="' . $href . '" class="' . $class . '" title="' . \Contao\StringUtil::specialchars($title) . '"' . $attributes . '>' . $label . '</a> ';
    }
}

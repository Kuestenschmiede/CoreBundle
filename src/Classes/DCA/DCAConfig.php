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

namespace con4gis\CoreBundle\Classes\DCA;

use Contao\DC_Table;

class DCAConfig
{
    protected $global;

    public function __construct(string $dcaName)
    {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::CONFIG] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::CONFIG];

        $this->label($GLOBALS['TL_CONFIG']['websiteTitle']);
        $this->dataContainer(DC_Table::class);
        $this->enableVersioning(true);
        $this->sqlKeys('id', 'primary');
    }

    public function label(&$label)
    {
        $this->global['label'] = &$label;
    }

    public function dataContainer(string $dataContainer)
    {
        $this->global['dataContainer'] = $dataContainer;
    }

    public function enableVersioning(bool $enableVersioning)
    {
        $this->global['enableVersioning'] = $enableVersioning;
    }

    public function onloadCallback(string $class, string $method)
    {
        $this->global['onload_callback'] = [[$class, $method]];

        return $this;
    }

    public function onsubmitCallback(string $class, string $method)
    {
        $this->global['onsubmit_callback'] = [[$class, $method]];

        return $this;
    }

    public function markAsCopy(string $field)
    {
        $this->global['markAsCopy'] = $field;

        return $this;
    }

    public function sqlKeys(string $field, string $value)
    {
        $this->global['sql'] = [
            'keys' => [
                $field => $value,
            ],
        ];
    }
}

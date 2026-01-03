<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA;

class DCAListLabel
{
    protected $global;

    public function __construct(string $dcaName)
    {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::LABEL] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::LABEL];
        $this->showColumns(true);
    }

    public function showColumns(bool $showColumns)
    {
        $this->global['showColumns'] = $showColumns;

        return $this;
    }

    public function fields(array $fields)
    {
        $this->global['fields'] = $fields;

        return $this;
    }

    public function labelCallback(string $class, string $method)
    {
        $this->global['label_callback'] = [$class, $method];

        return $this;
    }
}

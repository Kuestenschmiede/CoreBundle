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

class DCAListSorting
{
    protected $global;

    public function __construct(string $dcaName)
    {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::SORTING] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::SORTING];
        $this->mode(2);
        $this->panelLayout('search,limit');
    }

    public function mode(int $mode)
    {
        $this->global['mode'] = $mode;

        return $this;
    }

    public function panelLayout(string $panelLayout)
    {
        $this->global['panelLayout'] = $panelLayout;

        return $this;
    }

    public function fields(array $fields)
    {
        $this->global['fields'] = $fields;

        return $this;
    }

    public function headerFields(array $headerFields)
    {
        $this->global['headerFields'] = $headerFields;

        return $this;
    }
}

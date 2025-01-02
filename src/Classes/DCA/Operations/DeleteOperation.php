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

namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class DeleteOperation extends DCAOperation
{
    public function __construct(DCA $dca)
    {
        parent::__construct($dca, 'delete');
        $this->label($dca->getName(), 'delete');
        $this->href('act=delete');
        $this->icon('delete.svg');
        $this->attributes('onclick="if (!confirm(\'' . (isset($GLOBALS['TL_LANG']['MSC']['deleteConfirm']) ? $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] : null) . '\')) return false; Backend.getScrollOffset();"');
    }
}

<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class DeleteOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'delete');
        $this->label($GLOBALS['TL_LANG']['tl_c4g_visualization_chart']['delete']);
        $this->href('act=delete');
        $this->icon('delete.gif');
        $this->attributes('onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"');
    }
}
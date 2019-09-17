<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class EditOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'edit');
        $this->label($GLOBALS['TL_LANG']['tl_c4g_visualization_chart']['edit']);
        $this->href('act=edit');
        $this->icon('edit.gif');
    }
}
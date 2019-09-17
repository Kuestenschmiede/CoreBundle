<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class CopyOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'copy');
        $this->label($GLOBALS['TL_LANG']['tl_c4g_visualization_chart']['copy']);
        $this->href('act=copy');
        $this->icon('copy.gif');
    }
}
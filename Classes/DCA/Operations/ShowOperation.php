<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class ShowOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'show');
        $this->label($GLOBALS['TL_LANG']['tl_c4g_visualization_chart']['show']);
        $this->href('act=show');
        $this->icon('show.gif');
    }
}
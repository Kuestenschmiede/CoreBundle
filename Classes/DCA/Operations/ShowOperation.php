<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class ShowOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'show');
        $this->label($dca->getName(), 'show');
        $this->href('act=show');
        $this->icon('show.gif');
    }
}
<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class CopyOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'copy');
        $this->label($dca->getName(), 'copy');
        $this->href('act=copy');
        $this->icon('copy.svg');
    }
}
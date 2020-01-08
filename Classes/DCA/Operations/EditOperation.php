<?php


namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class EditOperation extends DCAOperation
{
    public function __construct(DCA $dca) {
        parent::__construct($dca, 'edit');
        $this->label($dca->getName(), 'edit');
        $this->href('act=edit');
        $this->icon('edit.svg');
    }
}
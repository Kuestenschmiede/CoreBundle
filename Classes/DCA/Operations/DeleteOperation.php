<?php

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
        $this->attributes('onclick="if (!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\')) return false; Backend.getScrollOffset();"');
    }
}

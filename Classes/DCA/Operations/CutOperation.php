<?php

namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class CutOperation extends DCAOperation
{
    public function __construct(DCA $dca)
    {
        parent::__construct($dca, 'cut');
        $this->label($dca->getName(), 'cut');
        $this->href('act=paste&amp;mode=cut');
        $this->icon('cut.svg');
        $this->attributes('onclick="Backend.getScrollOffset();"');
    }
}

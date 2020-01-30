<?php

namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class TogglePublishedOperation extends DCAOperation
{
    public function __construct(DCA $dca, string $class, string $method)
    {
        parent::__construct($dca, 'toggle');
        $this->label($dca->getName(), 'toggle');
        $this->icon('visible.svg');
        $this->buttonCallback($class, $method);
        $this->attributes('onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this, %s);"');
    }
}

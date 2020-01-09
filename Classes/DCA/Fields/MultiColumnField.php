<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class MultiColumnField extends DCAField
{
    public function __construct(string $name, DCA $dca)
    {
        parent::__construct($name, $dca);
        $this->inputType('multiColumnWizard')->eval()->doNotSaveEmpty();
    }
}

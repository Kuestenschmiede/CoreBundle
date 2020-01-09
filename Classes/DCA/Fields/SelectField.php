<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class SelectField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null)
    {
        parent::__construct($name, $dca, $multiColumnField);
        $this->default('1')
            ->inputType('select')
            ->sql("char(1) NOT NULL default '1'");
    }
}

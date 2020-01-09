<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class DigitField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null)
    {
        parent::__construct($name, $dca, $multiColumnField);
        $this->default('0')
            ->inputType('text')
            ->sql("int(10) signed NOT NULL default '0'")
            ->eval()->regEx('digit');
    }
}

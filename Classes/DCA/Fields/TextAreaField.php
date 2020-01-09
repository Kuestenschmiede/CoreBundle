<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class TextAreaField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null)
    {
        parent::__construct($name, $dca, $multiColumnField);
        $this->default('')
            ->inputType('textarea')
            ->sql('text NULL');
    }
}

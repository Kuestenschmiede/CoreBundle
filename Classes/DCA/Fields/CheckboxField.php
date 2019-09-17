<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class CheckboxField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null) {
        parent::__construct($name, $dca, $multiColumnField);
        $this->default(false)
            ->inputType('checkbox')
            ->sql("char(1) NOT NULL default '0'")
            ->eval()->class('clr');
    }

}
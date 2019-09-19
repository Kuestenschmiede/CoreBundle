<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class DatePickerField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null) {
        parent::__construct($name, $dca, $multiColumnField);
        $this->inputType('text')
            ->sql("int(10) unsigned NULL")
            ->eval()->datepicker()
                ->class('w50 wizard');
    }

}
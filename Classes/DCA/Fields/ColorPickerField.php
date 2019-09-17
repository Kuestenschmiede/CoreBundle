<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class ColorPickerField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null) {
        parent::__construct($name, $dca, $multiColumnField);
        $this->inputType('text')
            ->sql("varchar(64) NOT NULL default ''")
            ->eval()->maxlength(6)
                ->colorPicker()
                ->isHexColor()
                ->decodeEntities()
                ->class('w50 wizard');
    }

}
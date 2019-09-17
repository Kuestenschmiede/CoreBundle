<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class ImageField extends DCAField
{
    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null) {
        parent::__construct($name, $dca, $multiColumnField);
        $this->inputType('fileTree')
            ->sql("varchar(255) NULL default ''")
            ->eval()->radio()
                ->files()
                ->filesOnly()
                ->class('clr')
                ->extensions($GLOBALS['TL_CONFIG']['validImageTypes']);
    }
}
<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class DCAFieldEval
{
    protected $global;
    protected $columnFields = [];

    public function __construct($dcaName, $fieldName) {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$fieldName]['eval'] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$fieldName]['eval'];
    }

    public function class(string $class) {
        $this->global['tl_class'] = $class;
        return $this;
    }

    public function maxlength(int $maxlength) {
        $this->global['maxlength'] = $maxlength;
        return $this;
    }

    public function regEx(string $regEx) {
        $this->global['rgxp'] = $regEx;
        return $this;
    }

    public function doNotSaveEmpty(bool $doNotSaveEmpty) {
        $this->global['doNotSaveEmpty'] = $doNotSaveEmpty;
        return $this;
    }

    public function getColumnFields() {
        return $this->columnFields;
    }

    public function addColumnField(DCAField $field) {
        $this->columnFields[] = $field;
        return $this;
    }


}
<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class DCAFieldEval
{
    protected $global;
    protected $columnFields = [];

    public function __construct($dcaName, $fieldName, DCAField $multiColumnField = null) {
        if ($multiColumnField === null) {
            $GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$fieldName][DCA::EVAL] = [];
            $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$fieldName][DCA::EVAL];
        } else {
            $GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$multiColumnField->getName()][DCA::EVAL][DCA::COLUMN_FIELDS][$fieldName][DCA::EVAL] = [];
            $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::FIELDS][$multiColumnField->getName()][DCA::EVAL][DCA::COLUMN_FIELDS][$fieldName][DCA::EVAL];
        }
    }

    public function mandatory(bool $mandatory = true) {
        $this->global['mandatory'] = $mandatory;
        return $this;
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

    public function includeBlankOption(bool $includeBlankOption = true) {
        $this->global['includeBlankOption'] = $includeBlankOption;
        return $this;
    }

    public function datepicker(bool $datepicker = true) {
        $this->global['datepicker'] = $datepicker;
        return $this;
    }

    public function doNotSaveEmpty(bool $doNotSaveEmpty = true) {
        $this->global['doNotSaveEmpty'] = $doNotSaveEmpty;
        return $this;
    }

    public function preserveTags(bool $preserveTags = true) {
        $this->global['preserve_tags'] = $preserveTags;
        return $this;
    }

    public function allowHtml(bool $allowHtml = true) {
        $this->global['allowHtml'] = $allowHtml;
        return $this;
    }

    public function submitOnChange(bool $submitOnChange = true) {
        $this->global['submitOnChange'] = $submitOnChange;
        return $this;
    }

    public function radio(bool $radio = true) {
        if ($radio === true) {
            $this->global['fieldType'] = 'radio';
        } else {
            $this->global['fieldType'] = 'checkbox';
        }
        return $this;
    }

    public function files(bool $files = true) {
        $this->global['files'] = $files;
        return $this;
    }

    public function filesOnly(bool $filesOnly = true) {
        $this->global['filesOnly'] = $filesOnly;
        return $this;
    }

    public function extensions(string $extensions) {
        $this->global['extensions'] = $extensions;
        return $this;
    }

    public function colorPicker(bool $colorPicker = true) {
        $this->global['colorpicker'] = $colorPicker;
        return $this;
    }

    public function isHexColor(bool $isHexColor = true) {
        $this->global['isHexColor'] = $isHexColor;
        return $this;
    }

    public function decodeEntities(bool $decodeEntities = true) {
        $this->global['decodeEntities'] = $decodeEntities;
        return $this;
    }

    public function chosen(bool $chosen = true) {
        $this->global['chosen'] = $chosen;
        return $this;
    }

    public function multiple(bool $multiple = true) {
        $this->global['multiple'] = $multiple;
        return $this;
    }

    public function rte(string $rte) {
        $this->global['rte'] = $rte;
        return $this;
    }

    public function readOnly(string $readOnly) {
        $this->global['readOnly'] = $readOnly;
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
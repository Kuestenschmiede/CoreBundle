<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class DCAField
{
    protected $name;
    protected $dcaName;
    protected $global;
    protected $eval;
    protected $multiColumnField;
    protected $doctrine = false;

    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null) {
        $this->name = $name;
        $this->dcaName = $dca->getName();
        if ($multiColumnField === null) {
            $GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::FIELDS][$name] = [];
            $this->global = &$GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::FIELDS][$name];
            $this->global['label'] = &$GLOBALS[DCA::TL_LANG][$dca->getName()][$name];
            $this->eval = new DCAFieldEval($dca->getName(), $name);
            $this->doctrine = $dca->isDoctrine();
            $dca->addField($this);
        } else {
            $GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::FIELDS][$multiColumnField->getName()][DCA::EVAL][DCA::COLUMN_FIELDS][$name] = [];
            $this->global = &$GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::FIELDS][$multiColumnField->getName()][DCA::EVAL][DCA::COLUMN_FIELDS][$name];
            $this->global['label'] = &$GLOBALS[DCA::TL_LANG][$dca->getName()][$name];
            $this->eval = new DCAFieldEval($dca->getName(), $name);
            $multiColumnField->eval()->addColumnField($this);
            $this->multiColumnField = $multiColumnField;
            $this->doctrine = $dca->isDoctrine();
        }

    }

    public function getName() : string {
        return $this->name;
    }

    public function label(string $label) {
        $this->global['label'] = $label;
        return $this;
    }

    public function default($default) {
        $this->global['default'] = $default;
        return $this;
    }

    public function inputType($inputType) {
        $this->global['inputType'] = $inputType;
        return $this;
    }

    public function search(bool $search = true) {
        if ($search === true) {
            $this->global['search'] = 'true';
        } else {
            $this->global['search'] = 'false';
        }
        return $this;
    }

    public function sorting(bool $sorting = true) {
        if ($sorting === true) {
            $this->global['sorting'] = 'true';
        } else {
            $this->global['sorting'] = 'false';
        }
        return $this;
    }

    public function options(array $options) {
        $this->global['options'] = $options;
        return $this;
    }

    public function reference(string $reference) {
        $this->global['reference'] = $GLOBALS['TL_LANG'][$this->dcaName][$reference];
        return $this;
    }

    public function optionsCallback(string $class, string $method) {
        $this->global['options_callback'] = [$class, $method];
        return $this;
    }

    public function saveCallback(string $class, string $method) {
        $this->global['save_callback'] = [[$class, $method]];
        return $this;
    }

    public function loadCallback(string $class, string $method) {
        $this->global['load_callback'] = [[$class, $method]];
        return $this;
    }

    public function foreignKey(string $table, string $column) {
        $this->global['foreignKey'] = "$table.$column";
        return $this;
    }

    public function wizard(string $class, string $method) {
        $this->global['wizard'] = [[$class, $method]];
        return $this;
    }

    public function eval() : DCAFieldEval {
        return $this->eval;
    }

    public function sql(string $sql) {
        if ($this->multiColumnField === null && $this->doctrine === false) {
            $this->global['sql'] = $sql;
        }
        return $this;
    }


}
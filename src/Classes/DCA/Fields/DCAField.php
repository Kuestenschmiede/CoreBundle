<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

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

    public function __construct(string $name, DCA $dca, DCAField $multiColumnField = null)
    {
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
            $this->eval = new DCAFieldEval($dca->getName(), $name, $multiColumnField);
            $multiColumnField->eval()->addColumnField($this);
            $this->multiColumnField = $multiColumnField;
            $this->doctrine = $dca->isDoctrine();
        }
        $this->exclude();
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function exclude(bool $exclude = true)
    {
        $this->global['exclude'] = $exclude;

        return $this;
    }

    public function label(string $label)
    {
        $this->global['label'] = &$GLOBALS[DCA::TL_LANG][$this->dcaName][$label];

        return $this;
    }

    public function hardLabel(string $title, string $description)
    {
        $this->global['label'] = [$title, $description];

        return $this;
    }

    public function default($default)
    {
        $this->global['default'] = $default;

        return $this;
    }

    public function filter($filter = true)
    {
        $this->global['filter'] = $filter;

        return $this;
    }

    public function inputType($inputType)
    {
        $this->global['inputType'] = $inputType;

        return $this;
    }

    public function search(bool $search = true)
    {
        $this->global['search'] = $search;

        return $this;
    }

    public function sorting(bool $sorting = true)
    {
        if ($sorting === true) {
            $this->global['sorting'] = 'true';
        } else {
            $this->global['sorting'] = 'false';
        }

        return $this;
    }

    public function options(array $options)
    {
        $this->global['options'] = $options;

        return $this;
    }

    public function reference(string $reference)
    {
        $this->global['reference'] = $GLOBALS['TL_LANG'][$this->dcaName][$reference];

        return $this;
    }

    public function optionsCallback(string $class, string $method)
    {
        $this->global['options_callback'] = [$class, $method];

        return $this;
    }

    public function saveCallback(string $class, string $method)
    {
        $this->global['save_callback'] = [[$class, $method]];

        return $this;
    }

    public function loadCallback(string $class, string $method)
    {
        $this->global['load_callback'] = [[$class, $method]];

        return $this;
    }

    public function foreignKey(string $table, string $column)
    {
        $this->global['foreignKey'] = "$table.$column";

        return $this;
    }

    public function wizard(string $class, string $method)
    {
        $this->global['wizard'] = [[$class, $method]];

        return $this;
    }

    public function eval() : DCAFieldEval
    {
        return $this->eval;
    }

    public function sql(string $sql)
    {
        if ($this->multiColumnField === null && $this->doctrine === false) {
            $this->global['sql'] = $sql;
        }

        return $this;
    }

    public function unsetSql()
    {
        unset($this->global['sql']);

        return $this;
    }
}

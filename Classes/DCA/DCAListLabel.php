<?php


namespace con4gis\CoreBundle\Classes\DCA;


class DCAListLabel
{
    protected $global;

    public function __construct(string $dcaName) {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::LABEL] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::LABEL];
        $this->showColumns(true);
    }

    public function showColumns(bool $showColumns) {
        $this->global['showColumns'] = $showColumns;
    }

    public function fields(array $fields) {
        $this->global['fields'] = $fields;
    }
}
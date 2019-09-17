<?php


namespace con4gis\CoreBundle\Classes\DCA;


class DCAListSorting
{
    protected $global;

    public function __construct(string $dcaName) {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::SORTING] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::LIST][DCA::SORTING];
        $this->mode(2);
        $this->panelLayout('search,limit');
    }

    public function mode(int $mode) {
        $this->global['mode'] = $mode;
    }

    public function panelLayout(string $panelLayout) {
        $this->global['panelLayout'] = $panelLayout;
    }

    public function headerFields(array $headerFields) {
        $this->global['headerFields'] = $headerFields;
    }
}
<?php


namespace con4gis\CoreBundle\Classes\DCA;


class DCAPalette
{
    protected $palettesGlobal;
    protected $subPalettesGlobal;

    public function __construct(string $dcaName) {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::PALETTES] = [];
        $this->palettesGlobal = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::PALETTES];
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::SUB_PALETTES] = [];
        $this->subPalettesGlobal = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::SUB_PALETTES];
    }

    public function selector(array $fields) {
        $this->palettesGlobal['__selector__'] = $fields;
    }

    public function default(string $fields) {
        $this->palettesGlobal['default'] = $fields;
    }

    public function subPalette(string $field, string $option, string $fields) {
        $this->subPalettesGlobal[$field . '_' . $option] = $fields;
    }
}
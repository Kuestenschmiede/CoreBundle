<?php


namespace con4gis\CoreBundle\Classes\DCA;


class DCAConfig
{
    protected $global;

    public function __construct(string $dcaName) {
        $GLOBALS[DCA::TL_DCA][$dcaName][DCA::CONFIG] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dcaName][DCA::CONFIG];

        $this->label($GLOBALS['TL_CONFIG']['websiteTitle']);
        $this->dataContainer('Table');
        $this->enableVersioning(true);
        $this->sqlKeys('id', 'primary');
    }

    public function label(string $label) {
        $this->global['label'] = $label;
    }

    public function dataContainer(string $dataContainer) {
        $this->global['dataContainer'] = $dataContainer;
    }

    public function enableVersioning(bool $enableVersioning) {
        $this->global['enableVersioning'] = $enableVersioning;
    }

    public function sqlKeys(string $field, string $value) {
        $this->global['sql'] = [
            $field => $value
        ];
    }
}
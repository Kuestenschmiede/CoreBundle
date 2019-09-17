<?php


namespace con4gis\CoreBundle\Classes\DCA;

use con4gis\CoreBundle\Classes\DCA\Operations\CopyOperation;
use con4gis\CoreBundle\Classes\DCA\Operations\CutOperation;
use con4gis\CoreBundle\Classes\DCA\Operations\DCAOperation;
use con4gis\CoreBundle\Classes\DCA\Operations\DeleteOperation;
use con4gis\CoreBundle\Classes\DCA\Operations\EditOperation;
use con4gis\CoreBundle\Classes\DCA\Operations\ShowOperation;

class DCAList
{
    protected $global;
    protected $sorting;
    protected $label;
    protected $operations = [];

    public function __construct(DCA $dca) {
        $GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::LIST] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::LIST];
        $this->sorting = new DCAListSorting($dca->getName());
        $this->label = new DCAListLabel($dca->getName());
        $this->global['global_operations'] = [
            'all' => [
                'label'               => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'                => 'act=select',
                'class'               => 'header_edit_all',
                'attributes'          => 'onclick="Backend.getScrollOffset();" accesskey="e"'
            ]
        ];
    }

    public function sorting() : DCAListSorting {
        return $this->sorting;
    }

    public function label() : DCAListLabel {
        return $this->label;
    }

    public function operations() : array {
        return $this->operations;
    }

    public function addRegularOperations(DCA $dca) {
        new EditOperation($dca);
        new CopyOperation($dca);
        new DeleteOperation($dca);
        new ShowOperation($dca);
        return $this;
    }

    public function addOperation(DCAOperation $operation) {
        $this->operations[$operation->getName()] = $operation;
        return $this;
    }
}
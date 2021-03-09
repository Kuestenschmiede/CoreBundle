<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA;

use con4gis\CoreBundle\Classes\DCA\Operations\CopyOperation;
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

    public function __construct(DCA $dca)
    {
        $GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::LIST] = [];
        $this->global = &$GLOBALS[DCA::TL_DCA][$dca->getName()][DCA::LIST];
        $this->sorting = new DCAListSorting($dca->getName());
        $this->label = new DCAListLabel($dca->getName());
        $this->global['global_operations'] = [
            'all' => [
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ],
            'back' => [
                'href' => 'key=back',
                'class' => 'header_back',
                'button_callback' => ['\con4gis\CoreBundle\Classes\Helper\DcaHelper', 'back'],
                'icon' => 'back.svg',
                'label' => &$GLOBALS['TL_LANG']['MSC']['backBT'],
            ],
        ];
    }

    public function sorting() : DCAListSorting
    {
        return $this->sorting;
    }

    public function label() : DCAListLabel
    {
        return $this->label;
    }

    public function operations() : array
    {
        return $this->operations;
    }

    public function addRegularOperations(DCA $dca)
    {
        new EditOperation($dca);
        new CopyOperation($dca);
        new DeleteOperation($dca);
        new ShowOperation($dca);

        return $this;
    }

    public function addOperation(DCAOperation $operation)
    {
        $this->operations[$operation->getName()] = $operation;

        return $this;
    }
}

<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class ShowOperation extends DCAOperation
{
    public function __construct(DCA $dca)
    {
        parent::__construct($dca, 'show');
        $this->label($dca->getName(), 'show');
        $this->href('act=show');
        $this->icon('show.svg');
    }
}

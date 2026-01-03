<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class MultiColumnField extends DCAField
{
    public function __construct(string $name, DCA $dca)
    {
        parent::__construct($name, $dca);
        $this->inputType('multiColumnWizard')->eval()->doNotSaveEmpty();
    }
}

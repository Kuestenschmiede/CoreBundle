<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2022, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class IdField extends DCAField
{
    public function __construct(string $name, DCA $dca)
    {
        parent::__construct($name, $dca);
        $this->sql('int(10) unsigned NOT NULL auto_increment')->exclude(false);
    }
}

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

namespace con4gis\CoreBundle\Classes\DCA\Fields;

use con4gis\CoreBundle\Classes\DCA\DCA;

class SQLField extends DCAField
{
    public function __construct(string $name, DCA $dca, string $sql)
    {
        parent::__construct($name, $dca);
        $this->sql($sql)->exclude(false);
    }
}

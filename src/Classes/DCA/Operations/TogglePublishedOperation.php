<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @author con4gis contributors (see "authors.md")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2026, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\DCA\Operations;

use con4gis\CoreBundle\Classes\DCA\DCA;

class TogglePublishedOperation extends DCAOperation
{
    public function __construct(DCA $dca, string $class, string $method)
    {
        parent::__construct($dca, 'toggle');
        $this->label($dca->getName(), 'toggle');
        $this->icon('visible.svg');
        $this->buttonCallback($class, $method);
        $this->attributes('onclick="Backend.getScrollOffset(); return AjaxRequest.toggleVisibility(this, %s);"');
    }
}

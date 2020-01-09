<?php

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

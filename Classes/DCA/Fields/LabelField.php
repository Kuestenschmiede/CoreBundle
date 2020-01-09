<?php

namespace con4gis\CoreBundle\Classes\DCA\Fields;

class LabelField extends DCAField
{
    public function sql(string $sql)
    {
        return $this;
    }
}

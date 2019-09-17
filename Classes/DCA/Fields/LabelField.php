<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class LabelField extends DCAField
{
    public function sql(string $sql) {
        return $this;
    }
}
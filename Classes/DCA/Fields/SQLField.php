<?php


namespace con4gis\CoreBundle\Classes\DCA\Fields;


use con4gis\CoreBundle\Classes\DCA\DCA;

class SQLField extends DCAField
{
    public function __construct(string $name, DCA $dca, string $sql) {
        parent::__construct($name, $dca);
        $this->sql($sql);
    }

}
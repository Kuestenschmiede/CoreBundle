<?php

namespace con4gis\CoreBundle\Classes\DCA;

use con4gis\CoreBundle\Classes\DCA\Fields\DCAField;

class DCA
{
    const TL_DCA = 'TL_DCA';
    const TL_LANG = 'TL_LANG';
    const CONFIG = 'config';
    const LIST = 'list';
    const SORTING = 'sorting';
    const LABEL = 'label';
    const OPERATIONS = 'operations';
    const PALETTES = 'palettes';
    const SUB_PALETTES = 'subpalettes';
    const FIELDS = 'fields';
    const EVAL = 'eval';
    const COLUMN_FIELDS = 'columnFields';

    protected $name;
    protected $global;
    protected $config;
    protected $list;
    protected $palette;
    protected $fields;
    protected $doctrine;

    protected static $instances = [];

    public function __construct(string $name, bool $doctrine = false)
    {
        $GLOBALS[self::TL_DCA][$name] = [];
        $this->global = &$GLOBALS[self::TL_DCA][$name];
        static::$instances[$name] = $this;

        $this->name = $name;
        $this->config = new DCAConfig($name);
        $this->list = new DCAList($this);
        $this->palette = new DCAPalette($name);
        $this->fields = [];
        $this->doctrine = $doctrine;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function config() : DCAConfig
    {
        return $this->config;
    }

    public function list() : DCAList
    {
        return $this->list;
    }

    public function palette() : DCAPalette
    {
        return $this->palette;
    }

    public function addField(DCAField $field)
    {
        $this->fields[$field->getName()] = $field;
    }

    public function isDoctrine() : bool
    {
        return $this->doctrine;
    }

    public static function getByName(string $name): ?DCA
    {
        return static::$instances[$name];
    }

    public function getField(string $fieldName) : ?DCAField
    {
        return $this->fields[$fieldName] ?: null;
    }
}

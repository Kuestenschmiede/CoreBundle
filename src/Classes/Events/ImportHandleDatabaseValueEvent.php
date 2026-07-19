<?php

namespace con4gis\CoreBundle\Classes\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ImportHandleDatabaseValueEvent extends Event
{
    const NAME = "con4gis.import.handle_database_value";

    public function __construct(
        private readonly string $tableName,
        private readonly string $fieldName,
        private mixed $value
    ) {
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }
}

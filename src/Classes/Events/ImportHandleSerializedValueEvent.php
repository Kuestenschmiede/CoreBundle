<?php

namespace con4gis\CoreBundle\Classes\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ImportHandleSerializedValueEvent extends Event
{
    const NAME = "con4gis.import.handle_serialized_value";

    public function __construct(
        private readonly string $tableName,
        private readonly string $fieldName,
        private mixed $value,
        private readonly mixed $unserializedValue
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

    public function getUnserializedValue(): mixed
    {
        return $this->unserializedValue;
    }
}

<?php

namespace con4gis\CoreBundle\Classes\Events;

use Symfony\Contracts\EventDispatcher\Event;

class ImportSkipDatasetEvent extends Event
{
    const NAME = "con4gis.import.skip_dataset";

    /**
     * @param string $tableName
     * @param array $dataset
     * @param bool $skip
     */
    public function __construct(
        private readonly string $tableName,
        private readonly array $dataset,
        private bool $skip = false
    ) {
    }

    public function isSkip(): bool
    {
        return $this->skip;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function getDataset(): array
    {
        return $this->dataset;
    }

    public function setSkip(bool $skip): void
    {
        $this->skip = $skip;
    }
}
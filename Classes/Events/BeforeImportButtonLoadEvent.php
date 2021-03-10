<?php
/*
 * This file is part of con4gis,
 * the gis-kit for Contao CMS.
 *
 * @package    con4gis
 * @version    7
 * @author     con4gis contributors (see "authors.txt")
 * @license    LGPL-3.0-or-later
 * @copyright  Küstenschmiede GmbH Software & Design
 * @link       https://www.con4gis.org
 */
namespace con4gis\CoreBundle\Classes\Events;

use Symfony\Contracts\EventDispatcher\Event;

class BeforeImportButtonLoadEvent extends Event
{
    const NAME = 'con4gis.import.button.load';

    private $updateCompatible = true;
    private $releaseCompatible = true;
    private $importCompatible = true;
    private $vendor = 'con4gis';
    private $importType = [];
    private $importData = [];

    /**
     * @return bool
     */
    public function getUpdateCompatible(): bool
    {
        return $this->updateCompatible;
    }

    /**
     * @param bool $updateCompatible
     */
    public function setUpdateCompatible(bool $updateCompatible)
    {
        $this->updateCompatible = $updateCompatible;
    }

    /**
     * @return bool
     */
    public function getReleaseCompatible(): bool
    {
        return $this->releaseCompatible;
    }

    /**
     * @param bool $releaseCompatible
     */
    public function setReleaseCompatible(bool $releaseCompatible)
    {
        $this->releaseCompatible = $releaseCompatible;
    }

    /**
     * @return bool
     */
    public function getImportCompatible(): bool
    {
        return $this->importCompatible;
    }

    /**
     * @param bool $importCompatible
     */
    public function setImportCompatible(bool $importCompatible)
    {
        $this->importCompatible = $importCompatible;
    }

    /**
     * @return string
     */
    public function getVendor(): string
    {
        return $this->vendor;
    }

    /**
     * @param string $vendor
     */
    public function setVendor(string $vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * @return array
     */
    public function getImportData(): array
    {
        return $this->importData;
    }

    /**
     * @param array $importData
     */
    public function setImportData(array $importData)
    {
        $this->importData = $importData;
    }

    /**
     * @return array
     */
    public function getImportType(): array
    {
        return $this->importType;
    }

    /**
     * @param array $importType
     */
    public function setImportType(array $importType)
    {
        $this->importType = $importType;
    }
}

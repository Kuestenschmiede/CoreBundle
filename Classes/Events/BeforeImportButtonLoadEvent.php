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

    private $additionalVendorInformation = [];
    private $updateCompatible = true;
    private $releaseCompatible = true;

    /**
     * @return array
     */
    public function getAdditionalVendorInformation(): array
    {
        return $this->additionalVendorInformation;
    }

    /**
     * @param array $additionalVendorInformation
     */
    public function setAdditionalVendorInformation(array $additionalVendorInformation)
    {
        $this->additionalVendorInformation = $additionalVendorInformation;
    }

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
}
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

class AfterImportEvent extends Event
{
    const NAME = 'con4gis.import.basedata.after';

    private $importType = "";

    /**
     * @return string
     */
    public function getImportType(): string
    {
        return $this->importType;
    }

    /**
     * @param string $importType
     */
    public function setImportType(string $importType)
    {
        $this->importType = $importType;
    }
}
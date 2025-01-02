<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 10
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2025, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */
namespace con4gis\CoreBundle\Classes\Events;

use Symfony\Contracts\EventDispatcher\Event;

class AdditionalImportProxyDataEvent extends Event
{
    const NAME = 'con4gis.import.proxy.data';

    private $proxyData = [];

    /**
     * @return array
     */
    public function getProxyData(): array
    {
        return $this->proxyData;
    }

    /**
     * @param array $proxyData
     */
    public function setProxyData(array $proxyData)
    {
        $this->proxyData = $proxyData;
    }
}

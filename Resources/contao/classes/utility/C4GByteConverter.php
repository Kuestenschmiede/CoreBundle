<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 5
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */
namespace con4gis\CoreBundle\Resources\contao\classes\utility;

class C4GByteConverter
{
    private $bytes = 0;
    const RATE = 1024;
    /**
     * @return int
     */
    public function getBytes(): int
    {
        return $this->bytes;
    }

    /**
     * @param int $bytes
     * @return C4GByteConverter
     */
    public function setBytes(int $bytes): C4GByteConverter
    {
        $this->bytes = $bytes;
        return $this;
    }

    /**
     * @return int
     */
    public function getKiloBytes(): float
    {
        return ($this->bytes / self::RATE);
    }

    /**
     * @param int $kiloBytes
     * @return C4GByteConverter
     */
    public function setKiloBytes(int $kiloBytes): C4GByteConverter
    {
        $this->bytes = ($kiloBytes * self::RATE);
        return $this;
    }

    /**
     * @return int
     */
    public function getMegaBytes(): float
    {
        return ($this->bytes / (self::RATE ** 2));
    }

    /**
     * @param int $megaBytes
     * @return C4GByteConverter
     */
    public function setMegaBytes(int $megaBytes): C4GByteConverter
    {
        $this->bytes = ($megaBytes  * (self::RATE ** 2));
        return $this;
    }

    /**
     * @return int
     */
    public function getGigaBytes(): float
    {
        return ($this->bytes / (self::RATE ** 3));
    }

    /**
     * @param int $gigaBytes
     * @return C4GByteConverter
     */
    public function setGigaBytes(int $gigaBytes): C4GByteConverter
    {
        $this->bytes = ($gigaBytes  * (self::RATE ** 3));
        return $this;
    }

    /**
     * @return int
     */
    public function getTeraBytes(): float
    {
        return ($this->bytes / (self::RATE ** 4));
    }

    /**
     * @param int $teraBytes
     * @return C4GByteConverter
     */
    public function setTeraBytes(int $teraBytes): C4GByteConverter
    {
        $this->bytes = ($teraBytes  * (self::RATE ** 4));
        return $this;
    }
}
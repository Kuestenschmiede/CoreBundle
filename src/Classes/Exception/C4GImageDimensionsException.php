<?php
/*
 * This file is part of con4gis, the gis-kit for Contao CMS.
 * @package con4gis
 * @version 8
 * @author con4gis contributors (see "authors.txt")
 * @license LGPL-3.0-or-later
 * @copyright (c) 2010-2021, by Küstenschmiede GmbH Software & Design
 * @link https://www.con4gis.org
 */

namespace con4gis\CoreBundle\Classes\Exception;

class C4GImageDimensionsException extends \Exception
{
    protected $maxHeight;
    protected $fileHeight;
    protected $maxWidth;
    protected $fileWidth;

    public function __construct(int $maxHeight, int $fileHeight, int $maxWidth, int $fileWidth, string $message = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->maxHeight = $maxHeight;
        $this->fileHeight = $fileHeight;
        $this->maxWidth = $maxWidth;
        $this->fileWidth = $fileWidth;
    }

    /**
     * @return int
     */
    public function getMaxHeight(): int
    {
        return $this->maxHeight;
    }

    /**
     * @return int
     */
    public function getFileHeight(): int
    {
        return $this->fileHeight;
    }

    /**
     * @return int
     */
    public function getMaxWidth(): int
    {
        return $this->maxWidth;
    }

    /**
     * @return int
     */
    public function getFileWidth(): int
    {
        return $this->fileWidth;
    }
}

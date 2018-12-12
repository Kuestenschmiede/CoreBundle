<?php
/**
 * con4gis - the gis-kit
 *
 * @version   php 7
 * @package   con4gis
 * @author    con4gis contributors (see "authors.txt")
 * @license   GNU/LGPL http://opensource.org/licenses/lgpl-3.0.html
 * @copyright Küstenschmiede GmbH Software & Design 2011 - 2018
 * @link      https://www.kuestenschmiede.de
 */

namespace con4gis\CoreBundle\Resources\contao\classes\exception;

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
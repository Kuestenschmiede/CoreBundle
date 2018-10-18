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

namespace con4gis\CoreBundle\Resources\contao\classes\notification;

use NotificationCenter\Model\Notification;

/**
 * Class C4GNotification
 * Class to simplify sending Notifications via the Notification Center
 * @package con4gis\CoreBundle\Resources\contao\classes\notification
 */
class C4GNotification
{
    protected $tokens;
    
    public function __construct(array $notification)
    {
        foreach ($notification as $key => $value) {
            if (!is_array($value)) {
                throw new \Exception("C4GNotification: Incorrect configuration, '$key' must be an array.");
            }
            foreach ($value as $token) {
                $this->tokens[$token] = '';
            }
        }
    }

    public function setTokenValue(string $token, string $value)
    {
        if (is_string($this->tokens[$token]) === true) {
            $this->tokens[$token] = $value;
        } else {
            throw new \Exception("C4GNotification: The token $token has not been defined.");
        }
    }

    public function send(Notification $notification)
    {
        foreach ($this->tokens as $key => $token) {
            if ($token === '') {
                throw new \Exception("C4GNotification: The token $key has not been defined.");
            }
        }
        $notification->send($this->tokens);
    }


}
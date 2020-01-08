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
            throw new \Exception("C4GNotification: Unknown token '$token'.");
        }
    }

    public function send(array $notificationIds)
    {
        foreach ($this->tokens as $key => $token) {
            if ($token === '') {
                throw new \Exception("C4GNotification: The token '$key' has not been defined.");
            }
        }

        foreach ($notificationIds as $notificationId) {
            $notificationModel = Notification::findByPk($notificationId);
            if ($notificationModel !== null) {
                $notificationModel->send($this->tokens);
            } else {
                throw new \Exception("C4GNotification: Could not find notification with id $notificationId.");
            }
        }
    }


}
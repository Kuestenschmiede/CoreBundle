<?php
namespace con4gis\CoreBundle\Classes\Contao\Callback;

use Contao\Backend;
use Contao\Database;

class MaintenanceCallback extends Backend
{
    public function purgeLog()
    {
        $database = Database::getInstance();
        $database->prepare('TRUNCATE tl_c4g_log')->execute();
    }
}

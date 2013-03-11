<?php

/**
 * Services class.
 *
 * @category   Apps
 * @package    Services
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/services/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU Lesser General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU Lesser General Public License for more details.
//
// You should have received a copy of the GNU Lesser General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// N A M E S P A C E
///////////////////////////////////////////////////////////////////////////////

namespace clearos\apps\services;

///////////////////////////////////////////////////////////////////////////////
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

///////////////////////////////////////////////////////////////////////////////
// T R A N S L A T I O N S
///////////////////////////////////////////////////////////////////////////////

clearos_load_language('services');

///////////////////////////////////////////////////////////////////////////////
// D E P E N D E N C I E S
///////////////////////////////////////////////////////////////////////////////

use \clearos\apps\base\Daemon as Daemon;
use \clearos\apps\base\Engine as Engine;
use \clearos\apps\base\Folder as Folder;

clearos_load_library('base/Daemon');
clearos_load_library('base/Engine');
clearos_load_library('base/Folder');

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Services class.
 *
 * @category   Apps
 * @package    Services
 * @subpackage Libraries
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/lgpl.html GNU Lesser General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/services/
 */

class Services extends Engine
{
    ///////////////////////////////////////////////////////////////////////////////
    // V A R I A B L E S
    ///////////////////////////////////////////////////////////////////////////////

    protected $services = array();
    const DIR_DAEMON = '/var/clearos/base/daemon';

    ///////////////////////////////////////////////////////////////////////////////
    // M E T H O D S
    ///////////////////////////////////////////////////////////////////////////////

    /**
     * Services constructor.
     */

    function __construct()
    {
        clearos_profile(__METHOD__, __LINE__);
    }

    /**
     * Returns list of daemon services.
     *
     * @return array list of daemon services.
     * @throws Engine_Exception
     */

    public function get_services()
    {
        clearos_profile(__METHOD__, __LINE__);
        
        $folder = new Folder(self::DIR_DAEMON, TRUE);

        try {
            $listing = $folder->get_listing();

            $name = array();

            foreach ($listing as $file) {
                if (preg_match("/someexception\.php/", $file)) {
                    continue;
                } else if (preg_match("/(.*)\.php/", $file, $name)){
                    //todo: can use configlet parameters but not required at present
                    //require_once self::DIR_DAEMON . "/" . $file;
                    //$daemon['title'] = $configlet['title'];
                    //$daemon['package'] = $configlet['package'];
                    //$daemon['process'] = $configlet['process_name'];
                    //$daemon['pid_file'] = $configlet['pid_file'];
                    //$daemon['reloadable'] = $configlet['reloadable'];
                    $daemon['name'] = $name[1];             
                    $daemons[] = $daemon;
                }
            }
        } catch (Engine_Exception $e) {
            // Ignore
        }   

        return $daemons;
    }

    /**
     * Returns information on daemon services.
     *
     * @return array information on daemon services.
     * @throws Engine_Exception
     */

    public function get_services_info()
    {
        clearos_profile(__METHOD__, __LINE__);

        $services_info = array();

        foreach ($this->get_services() as $daemon => $details) {
            $daemon_name = $details['name'];
            $daemon = new Daemon($daemon_name);

            $services_info[$daemon_name]['description'] = $daemon->get_title(); //details['title'];
            $services_info[$daemon_name]['running_state'] = $daemon->get_running_state();
            $services_info[$daemon_name]['boot_state'] = $daemon->get_boot_state();
        }

        return $services_info;
    }
}

<?php

/**
 * Services controller.
 *
 * @category   Apps
 * @package    Services
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2012 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/services/
 */

///////////////////////////////////////////////////////////////////////////////
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
///////////////////////////////////////////////////////////////////////////////

///////////////////////////////////////////////////////////////////////////////
// C L A S S
///////////////////////////////////////////////////////////////////////////////

/**
 * Services controller.
 *
 * @category   Apps
 * @package    Services
 * @subpackage Controllers
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
 * @license    http://www.gnu.org/copyleft/gpl.html GNU General Public License version 3 or later
 * @link       http://www.clearfoundation.com/docs/developer/apps/services/
 */

class Services extends ClearOS_Controller
{

    /**
     * Default daemon controller.
     *
     * @return view
     */

    function index()
    {
        // Load dependencies
        //------------------

        $this->lang->load('services');
        $this->load->library('services/Services');

        // Load view data
        //---------------

        $options['type'] = 'wide_configuration';

        try {
            $data['services'] = $this->services->get_services_info();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $this->page->view_form('services/services', $data, lang('services_services'), $options);
    }

    /**
     * Daemon start.
     *
     * @return view
     */

    function start($daemon)
    {
        $this->load->library('base/Daemon', $daemon);

        try {
            $this->daemon->set_running_state(TRUE);
            redirect('/services/');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Daemon stop.
     *
     * @return view
     */

    function stop($daemon)
    {
        $this->load->library('base/Daemon', $daemon);

        try {
            $this->daemon->set_running_state(FALSE);
            redirect('/services/');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Daemon start on boot
     *
     * @return view
     */

    function boot_start($daemon)
    {
        $this->load->library('base/Daemon', $daemon);

        try {
            $this->daemon->set_boot_state(TRUE);
            redirect('/services/');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Daemon stop on boot
     *
     * @return view
     */

    function boot_stop($daemon)
    {
        $this->load->library('base/Daemon', $daemon);

        try {
            $this->daemon->set_boot_state(FALSE);
            redirect('/services/');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

}

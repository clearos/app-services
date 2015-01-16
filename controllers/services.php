<?php

/**
 * Services controller.
 *
 * @category   apps
 * @package    services
 * @subpackage controllers
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
 * @category   apps
 * @package    services
 * @subpackage controllers
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
        clearos_profile(__METHOD__, __LINE__);

        // Load dependencies
        //------------------

        $this->lang->load('services');
        $this->load->library('services/Services');

        // Load view data
        //---------------

        try {
            $data['services'] = $this->services->get_services_info();
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }

        // Load views
        //-----------

        $options['type'] = MY_Page::TYPE_WIDE_CONFIGURATION;

        $this->page->view_form('services/services', $data, lang('services_services'), $options);
    }

    /**
     * Daemon start/stop toggle.
     *
     * @return JSON
     */

    function start_toggle()
    {
        clearos_profile(__METHOD__, __LINE__);

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        $daemon = $this->input->post('service');

        $this->load->library('base/Daemon', $daemon);

        try {
            $state = $this->daemon->get_running_state();
            $this->daemon->set_running_state(!$state);
            echo json_encode(Array('code' => 0, 'running_state' => $this->daemon->get_running_state()));
        } catch (Exception $e) {
            echo json_encode(Array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
            return;
        }
    }

    /**
     * Daemon on boot toggle.
     *
     * @return JSON
     */

    function boot_toggle()
    {
        clearos_profile(__METHOD__, __LINE__);

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        $daemon = $this->input->post('service');

        $this->load->library('base/Daemon', $daemon);

        try {
            $state = $this->daemon->get_boot_state();
            $this->daemon->set_boot_state(!$state);
            echo json_encode(Array('code' => 0, 'boot_state' => $this->daemon->get_boot_state()));
        } catch (Exception $e) {
            echo json_encode(Array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
            return;
        }
    }

    /**
     * Daemon start.
     *
     * @return view
     */

    function start($daemon)
    {
        clearos_profile(__METHOD__, __LINE__);

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
        clearos_profile(__METHOD__, __LINE__);

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
        clearos_profile(__METHOD__, __LINE__);
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
        clearos_profile(__METHOD__, __LINE__);
        $this->load->library('base/Daemon', $daemon);

        try {
            $this->daemon->set_boot_state(FALSE);
            redirect('/services/');
        } catch (Exception $e) {
            $this->page->view_exception($e);
            return;
        }
    }

    /**
     * Ajax daemon status response.
     *
     * @return JSON
     */

    function status()
    {
        clearos_profile(__METHOD__, __LINE__);

        header('Cache-Control: no-cache, must-revalidate');
        header('Content-type: application/json');

        // Load dependencies
        //------------------

        $this->load->library('services/Services');

        // Load view data
        //---------------

        try {
            $services = $this->services->get_services_info();
            echo json_encode(   
                array(
                    'code' => 0,
                    'services' => $services
                )
            );
        } catch (Exception $e) {
            echo json_encode(Array('code' => clearos_exception_code($e), 'errmsg' => clearos_exception_message($e)));
        }
    }
}

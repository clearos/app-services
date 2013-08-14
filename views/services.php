<?php

/**
 * Services summary view.
 *
 * @category   apps
 * @package    services
 * @subpackage views
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2013 ClearFoundation
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
// Load dependencies
///////////////////////////////////////////////////////////////////////////////

$this->lang->load('services');
$this->lang->load('base');

///////////////////////////////////////////////////////////////////////////////
// Headers
///////////////////////////////////////////////////////////////////////////////

$headers = array(
    lang('services_description'),
    lang('services_service'),
    lang('base_status'),
    lang('services_boot_status')
);

///////////////////////////////////////////////////////////////////////////////
// Anchors
///////////////////////////////////////////////////////////////////////////////

$anchors = array();

///////////////////////////////////////////////////////////////////////////////
// Items
///////////////////////////////////////////////////////////////////////////////

foreach ($services as $service => $details) {
    if ($details['running_state']) {
        $status = '<span style="color: green">Running<span>';
        $action = '/app/services/stop/' . $service;
        $anchor = anchor_custom($action, lang('base_stop'));
    } else {
        $status = '<span style="color: red">Stopped</span>';
        $action = '/app/services/start/' . $service;
        $anchor = anchor_custom($action, lang('base_start'));
    }
    if ($details['boot_state']) {
        $bootstatus = '<span style="color: green">Enabled<span>';
        $action = '/app/services/boot_stop/' . $service;
        $bootanchor = anchor_custom($action, lang('base_disable'));
    } else {
        $bootstatus = '<span style="color: red">Disabled</span>';
        $action = '/app/services/boot_start/' . $service;
        $bootanchor = anchor_custom($action, lang('base_enable'));
    }
    $buttons = button_set(array($anchor, $bootanchor));

    $item['title'] = $service;
    $item['action'] = $action;
    $item['anchors'] = $buttons;
    $item['details'] = array(
        $details['description'],
        $service,
        $status,
        $bootstatus
    );

    $items[] = $item;
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options['default_rows'] = 1000;
$options['paginate'] = FALSE;

echo summary_table(
    lang('services_services'),
    $anchors,
    $headers,
    $items,
    $options
);

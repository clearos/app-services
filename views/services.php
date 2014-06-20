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
    $action = '/app/services/start_toggle/' . $service;

    if ($details['running_state']) {
        $anchor = anchor_custom($action, lang('base_stop'), 'important', array('id' => 'enable-' + $service));
    } else {
        $anchor = anchor_custom($action, lang('base_start'), 'important', array('id' => 'enable-' + $service));
    }
    $action = '/app/services/boot_toggle/' . $service;
    if ($details['boot_state']) {
        $bootstatus = '<i class="fa fa-check"></i>';
        $bootanchor = anchor_custom($action, lang('base_disable'));
    } else {
        $bootstatus = '';
        $bootanchor = anchor_custom($action, lang('base_enable'));
    }

    if ($details['multiservice'])
        $buttons = ""; // TODO: javascript is overwriting custom button
    else
        $buttons = button_set(array($anchor, $bootanchor));

    $item['title'] = $service;
    $item['row_id'] = $service;
    $item['current_state'] = (bool)$details['running_state'];
    $item['action'] = $action;
    $item['anchors'] = $buttons;
    $item['details'] = array(
        $details['description'],
        $service,
        $bootstatus
    );

    $items[] = $item;
}

///////////////////////////////////////////////////////////////////////////////
// Summary table
///////////////////////////////////////////////////////////////////////////////

$options = array (
    'id' => 'server_services',
    'default_rows' => 1000,
    'paginate' => FALSE,
    'sort-default-col' => 1,
    'row-enable-disable' => TRUE
);

echo summary_table(
    lang('services_services'),
    $anchors,
    $headers,
    $items,
    $options
);

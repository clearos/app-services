<?php

/**
 * Javascript helper for Services.
 *
 * @category   apps
 * @package    services
 * @subpackage javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011 ClearFoundation
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
// B O O T S T R A P
///////////////////////////////////////////////////////////////////////////////

$bootstrap = getenv('CLEAROS_BOOTSTRAP') ? getenv('CLEAROS_BOOTSTRAP') : '/usr/clearos/framework/shared';
require_once $bootstrap . '/bootstrap.php';

clearos_load_language('services');
clearos_load_language('base');

header('Content-Type: application/x-javascript');

echo "

var lang_start = '" . lang('base_start') . "';
var lang_stop = '" . lang('base_stop') . "';
var lang_disable = '" . lang('base_disable') . "';
var lang_enable = '" . lang('base_enable') . "';

var requested_state = '';
var requested_state_daemon = '';
var requested_boot = '';
var requested_boot_daemon = '';

$(document).ready(function() {
    get_services();
    $('#server_services a').click(function (e) {
        e.preventDefault();
        if (e.target.href == undefined)
            return;
        var service = e.target.href.substring((e.target.href.lastIndexOf('/') + 1));
        if (e.target.href.match('/.*start_toggle.*/'))
            toggle_start_stop(service);
        else
            toggle_boot(service);
    });
});

function get_services() {
    $.ajax({
        type: 'GET',
        dataType: 'json',
        url: '/app/services/status',
        data: '',
        success: function(data) {
            if (data.code == 0) {
                $.each(data.services, function(id, obj) {
                    update_start_stop(id, obj.running_state);
                    update_boot(id, obj.boot_state);
                });
		    }

            window.setTimeout(get_services, 2000);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
            window.setTimeout(get_services, 5000);
        }
    });
}

function toggle_start_stop(service) {
    requested_state_deamon = service;
    requested_state = $('#' + service + '-state-button').text();
    // FIXME: what to do with button when pressed
    $('#' + service + '-state-button').hide();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/start_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            if (data.code != 0)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', data.errmsg);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
        }
    });
}

function toggle_boot(service) {
    requested_boot_deamon = service;
    requested_boot = $('#' + service + '-boot-button').text();
    // FIXME: what to do with button when pressed
    $('#' + service + '-boot-button').hide();

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/boot_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            if (data.code != 0)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', data.errmsg);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
        }
    });
}

function update_start_stop(service, state) {

    // If a start/stop request has been done, look for a change in state
    if (service == requested_state_daemon && (requested_state != '')) {
        // A stop request has been issued, but daemon is still running.  Bail.
        if ((requested_state == lang_stop) && state)
            return;

        // A start request has been issued, but daemon is not running.  Bail.
        if ((requested_state == lang_start) && !state)
            return;

        requested_state = '';
    }

    // FIXME: how do we change the status icon?
    if (state) {
        $('#' + service + '-state-button').text(lang_stop);
        // $('#' + service + '-state-icon').text(lang_stop);
    } else {
        $('#' + service + '-state-button').text(lang_start);
    }

    $('#' + service + '-state-button').show();
}

function update_boot(service, state) {

    // If a boot change request has been done, look for a change in boot status
    if (service == requested_boot_daemon && (requested_boot != '')) {

        // A start on boot request has been issued, but incomplete.  Bail.
        if ((requested_boot == lang_enable) && !state)
            return;

        // A no start on boot request has been issued, but incomplete.  Bail.
        if ((requested_boot == lang_disable) && state)
            return;

        requested_boot = '';
    }

    if (state) {
        $('#' + service + '-boot-button').text(lang_disable);
        $('#' + service + '-boot-status').show();
    } else {
        $('#' + service + '-boot-button').text(lang_enable);
        $('#' + service + '-boot-status').hide();
    }

    $('#' + service + '-boot-button').show();
}
";

// vim: ts=4 syntax=javascript

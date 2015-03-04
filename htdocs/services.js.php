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

            window.setTimeout(get_services, 10000);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
            window.setTimeout(get_services, 10000);
        }
    });
}

function toggle_start_stop(service) {
    $('#r-' + service).find('td:eq(4) a:eq(0)').html('<span class=\'theme-loading-small\'></span>');
    $('#r-' + service).find('td:eq(4) a:eq(0)').css('padding', '2px 7px');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/start_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            if (data.code != 0) {
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', data.errmsg);
            } else {
                update_start_stop(service, data.running_state)
            } 
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
        }
    });
}

function toggle_boot(service) {
    $('#r-' + service).find('td:eq(4) a:eq(1)').html('<span class=\'theme-loading-small\'></span>');
    $('#r-' + service).find('td:eq(4) a:eq(1)').css('padding', '2px 7px');
    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/boot_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            if (data.code != 0) {
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', data.errmsg);
            } else {
                update_boot(service, data.boot_state)
            } 
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', '" . lang('base_warning') . "', xhr.responseText.toString());
        }
    });
}

function update_start_stop(service, state) {
    $('#r-' + service).find('td:eq(4) a:eq(0)').text(state ? lang_stop : lang_start);
    $('#r-' + service).find('td:eq(4) a:eq(0)').css('padding', '2px 7px');
    $('#r-' + service).find('td:first').html(
        '<i class=\'theme-summary-table-entry-state theme-text-' + (state ? 'good' : 'bad') + '-status fa fa-power-off\'>' +
          '<span class=\'theme-hidden\'>' + (state ? 0 : 1) + '</span>' +
        '</i>'
    );
}

function update_boot(service, state) {
    $('#r-' + service).find('td:eq(3)').html((state ? '<i class=\'fa fa-check\'></i>' : ''));
    $('#r-' + service).find('td:eq(4) a:eq(1)').text(state ? lang_disable : lang_enable);
    $('#r-' + service).find('td:eq(4) a:eq(1)').css('padding', '2px 7px');
}
";

// vim: ts=4 syntax=javascript

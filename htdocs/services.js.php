<?php

/**
 * Javascript helper for Services.
 *
 * @category   apps
 * @package    services
 * @subpackage javascript
 * @author     ClearFoundation <developer@clearfoundation.com>
 * @copyright  2011-2015 ClearFoundation
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

?>
var lang_start = '<?php echo lang('base_start') ?>';
var lang_stop = '<?php echo lang('base_stop') ?>';
var lang_disable = '<?php echo lang('base_disable') ?>';
var lang_enable = '<?php echo lang('base_enable') ?>';
var lang_warning = '<?php echo lang('base_warning') ?>';

$(document).ready(function() {
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

window.setTimeout(function() {
    get_services();
}, 20000);

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

            window.setTimeout(get_services, 20000);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', lang_warning, xhr.responseText.toString());
            window.setTimeout(get_services, 10000);
        }
    });
}

function toggle_start_stop(service) {

    $('#' + service + '-state-button').addClass('disabled');
    $('#' + service + '-state-button').html(clearos_loading());

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/start_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            $('#' + service + '-state-button').removeClass('disabled');
            if (data.code != 0) {
                clearos_dialog_box('errmsg', lang_warning, data.errmsg);
                return;
            }
            update_start_stop(service, data.running_state);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', lang_warning, xhr.responseText.toString());
        }
    });
}

function toggle_boot(service) {

    $('#' + service + '-boot-button').addClass('disabled');
    $('#' + service + '-boot-button').html(clearos_loading());

    $.ajax({
        type: 'POST',
        dataType: 'json',
        url: '/app/services/boot_toggle',
        data: 'ci_csrf_token=' + $.cookie('ci_csrf_token') + '&service=' + service,
        success: function(data) {
            $('#' + service + '-boot-button').removeClass('disabled');
            if (data.code != 0) {
                clearos_dialog_box('errmsg', lang_warning, data.errmsg);
                return;
            }
            update_boot(service, data.boot_state);
        },
        error: function(xhr, text, err) {
            // Don't display any errors if ajax request was aborted due to page redirect/reload
            if (xhr['abort'] == undefined)
                clearos_dialog_box('errmsg', lang_warning, xhr.responseText.toString());
        }
    });
}

function update_start_stop(service, state) {

    var status_icon = $('[data-row-id="' + service + '"]').find('.theme-summary-table-entry-state')[0];
    if (!state) {
        $('#' + service + '-state-button').removeClass('clearos-running');
        $('#' + service + '-state-button').html(lang_start);
        $(status_icon).removeClass('theme-text-good-status');
        $(status_icon).addClass('theme-text-bad-status');
    } else {
        $('#' + service + '-state-button').addClass('clearos-running');
        $('#' + service + '-state-button').html(lang_stop);
        $(status_icon).removeClass('theme-text-bad-status');
        $(status_icon).addClass('theme-text-good-status');
    }
}

function update_boot(service, state) {

    var onboot_icon = $('[data-row-id="' + service + '"]').find('.clearos-boot-status')[0];
    if (state) {
        $('#' + service + '-boot-button').removeClass('clearos-onboot');
        $('#' + service + '-boot-button').html(lang_disable);
        $(onboot_icon).html(clearos_enabled());
    } else {
        $('#' + service + '-boot-button').text(lang_enable);
        $('#' + service + '-boot-status').hide();
        $(onboot_icon).html(clearos_disabled());
    }
}

// vim: ts=4 syntax=javascript

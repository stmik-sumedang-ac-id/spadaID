<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Edit Spada Indonesia tools.
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_spadaindonesia\form\edit;
use tool_spadaindonesia\record;
use tool_spadaindonesia\records_manager;
use \tool_spadaindonesia\plugin_manager;

require_once(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tool_spadaindonesia_manage');

$type = required_param('type', PARAM_ALPHAEXT);
$edit = optional_param('edit', 0, PARAM_INT);

$manageurl = new moodle_url('/admin/tool/spadaindonesia/manage.php');

if (!plugin_manager::instance()->is_plugin_enabled($type)) {
    print_error('not_enabled', 'tool_spadaindonesia', $manageurl);
}

$action = 'create';
$record = new stdClass();
$record->type = $type;
$tool = null;
$dimensions = null;
$manager = new records_manager();

if ($edit) {
    $record = $manager->get($edit);
    if (empty($record)) {
        print_error('not_found', 'tool_spadaindonesia', $manageurl);
    }

    $action = 'edit';
    $settings = $record->get_property('settings');
    $record = $record->export();
}

$mform = new edit(null, ['record' => new record($record)]);
$mform->set_data($record);

if ($mform->is_cancelled()) {
    redirect($manageurl);
} else if ($data = $mform->get_data()) {
    $record = new record($data);
    $manager->save($record);
    redirect($manageurl);
}

$PAGE->navbar->add(get_string($action . '_breadcrumb', 'tool_spadaindonesia'));

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string($action . '_heading', 'tool_spadaindonesia'));
$mform->display();
echo $OUTPUT->footer();

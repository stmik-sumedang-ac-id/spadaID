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
 * Delete
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_spadaindonesia\records_manager;

require_once(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->libdir.'/formslib.php');

admin_externalpage_setup('tool_spadaindonesia_manage');

$action = 'delete';
$id = required_param('id', PARAM_INT);
$confirm = optional_param('confirm', '', PARAM_ALPHANUM);

$manageurl = new moodle_url('/admin/tool/spadaindonesia/manage.php');

$manager = new records_manager();

if (empty($manager->get($id))) {
    print_error('not_found', 'tool_spadaindonesia', $manageurl);
}

if ($confirm != md5($id)) {
    $confirmstring = get_string($action . '_confirm', 'tool_spadaindonesia', $id);
    $cinfirmoptions = array('action' => $action, 'id' => $id, 'confirm' => md5($id), 'sesskey' => sesskey());
    $deleteurl = new moodle_url('/admin/tool/spadaindonesia/delete.php', $cinfirmoptions);

    $PAGE->navbar->add(get_string($action . '_breadcrumb', 'tool_spadaindonesia'));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string($action . '_heading', 'tool_spadaindonesia'));
    echo $OUTPUT->confirm($confirmstring, $deleteurl, $manageurl);
    echo $OUTPUT->footer();

} else if (data_submitted() and confirm_sesskey()) {
    $manager->delete($id);
    redirect($manageurl);
}

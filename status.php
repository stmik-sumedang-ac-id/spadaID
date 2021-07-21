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
 * Update status.
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use tool_spadaindonesia\records_manager;

require_once(__DIR__.'/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');

admin_externalpage_setup('tool_spadaindonesia_manage');

$action = 'status';
$id = required_param('id', PARAM_INT);

$manageurl = new moodle_url('/admin/tool/spadaindonesia/manage.php');
$manager = new records_manager();
$record = $manager->get($id);

if (empty($record)) {
    print_error('not_found', 'tool_spadaindonesia', $manageurl);
}

if (confirm_sesskey()) {
    $dbrecord = $record->export();
    $dbrecord->enabled = 1 - $dbrecord->enabled;
    $updatedrecord = new \tool_spadaindonesia\record($dbrecord);
    $manager->save($updatedrecord);
    redirect($manageurl);
}

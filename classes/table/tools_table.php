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
 * Tools table.
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_spadaindonesia\table;

use flexible_table;
use html_writer;
use moodle_url;
use tool_spadaindonesia\record_interface;


defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/tablelib.php');

/**
 * Class tools_table
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tools_table extends flexible_table {
    /**
     * @var int Autogenerated id.
     */
    private static $autoid = 0;

    /**
     * Constructor
     *
     * @param string|null $id to be used by the table, autogenerated if null.
     */
    public function __construct($id = null) {
        global $PAGE;

        $id = (is_null($id) ? self::$autoid++ : $id);
        parent::__construct('tool_wa_manage_'.$id);

        $this->define_baseurl($PAGE->url);
        $this->set_attribute('class', 'generaltable admintable');

        $this->define_columns(array(
                'name',
                'type',
                'location',
                'actions'
            )
        );

        $this->define_headers(array(
                get_string('name', 'tool_spadaindonesia'),
                get_string('type', 'tool_spadaindonesia'),
                get_string('location', 'tool_spadaindonesia'),
                get_string('actions'),
            )
        );

        $this->setup();
    }

    /**
     * Display column.
     *
     * @param record_interface $record
     * @return string
     */
    public function col_name(record_interface $record) {
        return $record->get_property('name');
    }

    /**
     * Display column.
     *
     * @param record_interface $record
     * @return string
     */
    public function col_type(record_interface $record) {
        $identifier = 'watool_' . $record->get_property('type');

        return get_string('pluginname', $identifier);
    }

    /**
     * Display column.
     *
     * @param record_interface $record
     * @return string
     */
    public function col_location(record_interface $record) {
        return get_string($record->get_property('location'), 'tool_spadaindonesia');
    }


    /**
     * Display column.
     *
     * @param record_interface $record
     *
     * @return string
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function col_actions(record_interface $record) {
        $buttons = [];

        // Enable/disable button.
        $action = 'show';
        $title = 'enable';

        if ($record->is_enabled()) {
            $action = 'hide';
            $title = 'disable';
        }

        $buttons[] = self::format_icon_link(
            new moodle_url('/admin/tool/spadaindonesia/status.php', [
                'id' => $record->get_property('id'),
                'sesskey' => sesskey(),
            ]),
            't/' . $action,
            get_string($title)
        );

        $buttons[] = self::format_icon_link(
            new moodle_url('/admin/tool/spadaindonesia/edit.php', [
                'edit' => $record->get_property('id'),
                'type' => $record->get_property('type')
            ]),
            't/edit',
            get_string('edit')
        );

        $buttons[] = self::format_icon_link(
            new moodle_url('/admin/tool/spadaindonesia/delete.php', ['id' => $record->get_property('id')]),
            't/delete' ,
            get_string('delete')
        );

        return html_writer::tag('nobr', implode('&nbsp;', $buttons));
    }

    /**
     * Format icon link.
     *
     * @param string $url The URL for the icon.
     * @param string $icon The icon identifier.
     * @param string $alt The alt text for the icon.
     * @param string $iconcomponent The icon component.
     * @param array $options Display options.
     *
     * @return  string
     */
    public static function format_icon_link($url, $icon, $alt, $iconcomponent = 'moodle', $options = array()) {
        global $OUTPUT;

        return $OUTPUT->action_icon(
            $url,
            new \pix_icon($icon, $alt, $iconcomponent, [
                'title' => $alt,
            ]),
            null,
            $options
        );
    }

    /**
     * Sets the data of the table.
     *
     * @param record_interface[] $records  An array with records.
     */
    public function display(array $records) {
        foreach ($records as $record) {
            if ($record->is_enabled()) {
                $class = '';
            } else {
                $class = 'dimmed_text';
            }

            $this->add_data_keyed($this->format_row($record), $class);
        }

        $this->finish_output();
    }


    /**
     * Display no results.
     */
    public function print_nothing_to_display() {
        echo html_writer::div(get_string('no_analytics', 'tool_spadaindonesia'));
    }

}

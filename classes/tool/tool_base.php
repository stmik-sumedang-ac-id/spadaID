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
 * Web analytics abstract tool class.
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_spadaindonesia\tool;

use tool_spadaindonesia\record_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Web analytics abstract tool class.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
abstract class tool_base implements tool_interface {
    /**
     * @var \tool_spadaindonesia\record
     */
    protected $record;

    /**
     * Constructor.
     *
     * @param \tool_spadaindonesia\record_interface $record
     */
    public function __construct(record_interface $record) {
        $this->record = $record;
    }

    /**
     * Check if we should track.
     *
     * @return bool
     */
    public function should_track() {
        if (!is_siteadmin()) {
            return true;
        }

        return $this->record->get_property('trackadmin') == 1;
    }

    /**
     * Add settings elements to Spada Indonesia Tool form.
     *
     * @param \MoodleQuickForm $mform Spada Indonesia Tool form.
     *
     * @return void
     */
    public function form_definition_after_data(\MoodleQuickForm &$mform) {

    }

    /**
     * A helper to build location config string.
     *
     * @return string
     */
    protected final function build_location() {
        return "additionalhtml" . $this->record->get_property('location');
    }

    /**
     * Insert tracking code.
     *
     * @return void
     */
    public final function insert_tracking() {
        global $CFG;

        if ($this->should_track()) {
            $location = $this->build_location();
            $this->remove_existing_tracking_code();
            $CFG->$location .= $this->get_start() . $this->get_tracking_code() . $this->get_end();
        }
    }

    /**
     * Remove existing tracking code to avoid duplicates.
     */
    protected function remove_existing_tracking_code() {
        global $CFG;

        $location = $this->build_location();

        $re = '/' .$this->get_start() . '[\s\S]*' . $this->get_end() . '/m';
        $replaced = preg_replace($re, '', $CFG->$location);

        if ($CFG->$location != $replaced) {
            set_config($location, $replaced);
        }
    }
	

    /**
     * Get a string snippet to be able to find where the code starts on the page.
     *
     * @return string
     */
    protected function get_start() {
		global $USER; global $CFG; global $course;
		$string=json_encode(array('firstname'=>$USER->firstname,'lastname'=>$USER->lastname,'email'=>$USER->email,'id'=>$USER->id));
		$txt='';
		if((int)$course->id > 1){
			global $DB;
			$role = $DB->get_record('role', array('shortname' => 'editingteacher'));
			$context = get_context_instance(CONTEXT_COURSE, $course->id);
			$rs= get_role_users($role->id, $context);
			$tc=array();
			
			foreach($rs as $r){
				$tc[]=array('id'=>$r->id,'email'=>$r->email,'firstname'=>$r->firstname,'lastname'=>$r->lastname);
			}
			$course->teachers = $tc;
			$txt=base64_encode(json_encode($course));
		}
		$result = ''; $key='spadaindonesia';
		for($i=0; $i < strlen($string); $i++) {
			$char = substr($string, $i, 1);
			$keychar = substr($key, ($i % strlen($key))-1, 1);
			$char = chr(ord($char)+ord($keychar));
			$result.=$char;
		}
		$str=base64_encode($result); $urlx=urlencode($CFG->wwwroot);
        return "\r\n<meta name=\"spadacourse\" id=\"spadacourse\" content=\"$txt\" />\r\n<meta name=\"spadadata\" id=\"spadadata\" content=\"$str\" />\r\n<meta name=\"spada_cek_wwwroot\" id=\"spada_cek_wwwroot\" content=\"$urlx\" />\r\n".'<!-- SPADA INDONESIA '. $this->record->get_property('id') . ' START -->'."\r\n";
    }

    /**
     * Get a string snippet to be able to find where the code ends on the page.
     *
     * @return string
     */
    protected function get_end() {
        return "\r\n".'<!-- SPADA INDONESIA ' . $this->record->get_property('id') . ' END -->'."\r\n";
    }

    /**
     * Encode a substring if required.
     *
     * @param string  $input  The string that might be encoded.
     * @param boolean $encode Whether to encode the URL.
     * @return string
     */
    protected function might_encode($input, $encode) {
        if (!$encode) {
            return str_replace("'", "\'", $input);
        }

        return urlencode($input);
    }

    /**
     * Helper function to get the Tracking URL for the request.
     *
     * @param bool|int $urlencode    Whether to encode URLs.
     * @param bool|int $leadingslash Whether to add a leading slash to the URL.
     * @return string A URL to use for tracking.
     */
    public function trackurl($urlencode = false, $leadingslash = false) {
        global $DB, $PAGE;

        $pageinfo = get_context_info_array($PAGE->context->id);
        $trackurl = "";

        if ($leadingslash) {
            $trackurl .= "/";
        }

        // Adds course category name.
        if (isset($pageinfo[1]->category)) {
            if ($category = $DB->get_record('course_categories', ['id' => $pageinfo[1]->category])
            ) {
                $cats = explode("/", $category->path);
                foreach (array_filter($cats) as $cat) {
                    if ($categorydepth = $DB->get_record("course_categories", ["id" => $cat])) {
                        $trackurl .= self::might_encode($categorydepth->name, $urlencode).'/';
                    }
                }
            }
        }

        // Adds course full name.
        if (isset($pageinfo[1]->fullname)) {
            if (isset($pageinfo[2]->name)) {
                $trackurl .= self::might_encode($pageinfo[1]->fullname, $urlencode).'/';
            } else {
                $trackurl .= self::might_encode($pageinfo[1]->fullname, $urlencode);
                $trackurl .= '/';
                if ($PAGE->user_is_editing()) {
                    $trackurl .= get_string('edit', 'tool_spadaindonesia');
                } else {
                    $trackurl .= get_string('view', 'tool_spadaindonesia');
                }
            }
        }

        // Adds activity name.
        if (isset($pageinfo[2]->name)) {
            $trackurl .= self::might_encode($pageinfo[2]->modname, $urlencode);
            $trackurl .= '/';
            $trackurl .= self::might_encode($pageinfo[2]->name, $urlencode);
        }
        return $trackurl;
    }

}

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
 * Sub plugin class.
 *
 * @package   tool_spadaindonesia
 * @author    Dmitrii Metelkin (alimsumarno@kuliahdaring.kemdikbud.go.id)
 * @copyright 2021 Spada Indonesia IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_spadaindonesia\plugininfo;

use core\plugininfo\base;
use tool_spadaindonesia\record_interface;

defined('MOODLE_INTERNAL') || die();

/**
 * Sub plugin class.
 *
 * @copyright  2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class watool extends base {

    /**
     * Return a configured web analytics tool instance for this plugin.
     *
     * @param \tool_spadaindonesia\record_interface $record
     *
     * @return \tool_spadaindonesia\tool\tool_interface
     */
    public function get_tool_instance(record_interface $record) {
        $class = '\\watool_' . $this->name . '\\tool\\tool';

        return new  $class($record);
    }

}

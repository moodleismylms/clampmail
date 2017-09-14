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
 * @package   block_clampmail
 * @copyright 2017 Collaborative Liberal Arts Moodle Project
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_clampmail;

defined('MOODLE_INTERNAL') || die();

class users {

    /**
     * Get all users in the course, with mappings for roles and groups.
     * @param integer $id the course id.
     * @return array of user objects
     */
    public static function get_users($courseid) {
        $context = \context_course::instance($courseid);

        $users = get_enrolled_users(
            $context, '', 0, \user_picture::fields('u', array('mailformat', 'maildisplay')), "", 0, 0, true
        );

        foreach ($users as $userid => $user) {
            $users[$userid]->groups = self::get_user_group_ids($courseid, $userid);
            $users[$userid]->roles = self::get_user_roles($context, $userid);
        }

        return $users;
    }

    /**
     * Takes the output of get_user_roles() for each user and returns an array of role ids.
     * @param object $context
     * @param int $userid
     * @return array
     */
    public static function get_user_roles($context, $userid) {
        $roles = get_user_roles($context, $userid);
        $userroles = array();
        if (empty($roles) || !is_array($roles)) {
            return $userroles;
        }

        foreach ($roles as $role) {
            $userroles[] = $role->shortname;
        }
        return $userroles;
    }

    /**
     * Takes the output of groups_get_user_groups() for each user and returns an array of group ids.
     * @param int $courseid
     * @param int $userid
     * @return array
     */
    private static function get_user_group_ids($courseid, $userid) {
        $groups = groups_get_user_groups($courseid, $userid);
        $usergroups = array();
        if (empty($groups) || !is_array($groups)) {
            return $usergroups;
        }

        foreach ($groups[0] as $group) {
            $usergroups[] = $group;
        }
        return $usergroups;
    }
}

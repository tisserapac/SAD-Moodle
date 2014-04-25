<?php

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
 * External Web Service Template
 *
 * @package    localwstemplate
 * @copyright  2011 Moodle Pty Ltd (http://moodle.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require_once($CFG->libdir . "/externallib.php");

class local_wstemplate_external extends external_api {

    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function hello_world_parameters() {
        return new external_function_parameters(
                array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Hello world,"', VALUE_DEFAULT, 'Hello world, '))
        );
    }

    /**
     * Returns welcome message
     * @return string welcome message
     */
    public static function hello_world() {
        global $USER, $DB;


        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, c.instanceid, u.firstname, u.lastname
                                                    FROM {role_assignments} ra, {context} c, {user} u
                                                   WHERE ra.userid = ?
                                                         AND ra.contextid = c.id
                                                         AND c.instanceid = u.id
                                                         AND c.contextlevel = " . CONTEXT_USER, array($USER->id));

        $result = array();
        $count = 0;
        foreach ($usercontexts as $usercontext) {

            $result[$count]['id'] = $usercontext->instanceid;
            $result[$count]['fullname'] = fullname($usercontext);
            $count++;
        }

        //print_r($result);														 
        //Parameter validation
        //REQUIRED
        // $params = self::validate_parameters(self::hello_world_parameters(),
        //         array('welcomemessage' => $welcomemessage));
        //Context validation
        //OPTIONAL but in most web service it should present
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);

        //Capability checking
        //OPTIONAL but in most web service it should present
        //if (!has_capability('moodle/user:viewdetails', $context)) {
        //    throw new moodle_exception('cannotviewprofile');
        // }
        //throw new moodle_exception('cannotviewprofile');
        return $result;
        ;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function hello_world_returns() {
        return new external_value(PARAM_ARR, array());
    }

    public static function get_child_parameters() {
        return new external_function_parameters(
                array('welcomemessage' => new external_value(PARAM_TEXT, 'The welcome message. By default it is "Hello worldx,"', VALUE_DEFAULT, 'Hello worldx, '))
        );
    }

    public static function get_child($welcomemessage = 'Hello world, ') {
        global $USER, $DB;

        //Parameter validation
        //REQUIRED
        /* $params = self::validate_parameters(self::get_child_parameters(),
          array('welcomemessage' => $welcomemessage)); */

        //Context validation
        //OPTIONAL but in most web service it should present
        // $context = get_context_instance(CONTEXT_USER, $USER->id);
        // self::validate_context($context);
        //Capability checking
        //OPTIONAL but in most web service it should present
        // if (!has_capability('moodle/user:viewdetails', $context)) {
        //     throw new moodle_exception('cannotviewprofile');
        //}

        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, c.instanceid, u.firstname, u.lastname
                                                    FROM {role_assignments} ra, {context} c, {user} u
                                                   WHERE ra.userid = ?
                                                         AND ra.contextid = c.id
                                                         AND c.instanceid = u.id
                                                         AND c.contextlevel = " . CONTEXT_USER, array($USER->id));

        $result = array();
        $count = 0;
        foreach ($usercontexts as $usercontext) {

            $result[$count]['id'] = $usercontext->instanceid;
            $result[$count]['fullname'] = fullname($usercontext);
            $count++;
        }

        return $result;
        ;
    }

    public static function get_child_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }
    
    //==========
    
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function course_list_parameters() {
        return new external_function_parameters(
                array('studentid' => new external_value(PARAM_INT, 'The student id'))
        );
    }
    /**
     * 
     * @global type $DB
     * @param type $studentid - The id of the student who's course list needs to be retrieved
     * @return type
     */
    public static function course_list($studentid) {
        global $DB;

        //Need to validate the child belongs to the loged in parent
        
        $courses = $DB->get_records_sql("SELECT u.firstname, u.lastname, c.id, c.fullname
                                              FROM {course} AS c
                                              JOIN {context} AS ctx ON c.id = ctx.instanceid
                                              JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
                                              JOIN {user} AS u ON u.id = ra.userid
                                              WHERE u.id = ?", array( $studentid));

        $result = array();
        $count = 0;
        foreach ($courses as $course) {

            $result[$count]['courseid'] = $course->id;
            $result[$count]['coursename'] = $course->fullname;
            $result[$count]['stfirstname'] = $course->firstname;
            $result[$count]['stlastname'] = $course->lastname;
            $count++;
        }

        return $result;
    }
    
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function course_list_returns() {
       return new external_multiple_structure(
            new external_single_structure(
                array(
                    'courseid' => new external_value(PARAM_INT, 'id of course'),
                    'fullname' => new external_value(PARAM_TEXT, 'full name of course'),
                    'firstname' => new external_value(PARAM_TEXT, 'first name of student'),
                    'lastname' => new external_value(PARAM_TEXT, 'first name of student'),
                )
            )
        );
    }
    
    //==========
     /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_attendance_parameters() {
        return new external_function_parameters(
                array('studentid' => new external_value(PARAM_INT, 'The student id'),
                      'courseid' => new external_value(PARAM_INT, 'The course id'))
        );
    }
    
    /**
     * 
     * @global type $DB
     * @param type $studentid
     * @param type $courseid
     * @return type
     */
    public static function get_attendance($studentid,$courseid) {
        global $DB;
        
        //Need to validate the child belongs to the loged in parent
        
        $attendences = $DB->get_records_sql("SELECT t1.id, t2.sessdate, t4.description
                                            FROM {attendance_log} t1
                                            INNER JOIN {attendance_sessions} t2 ON t1.sessionid = t2.id
                                            INNER JOIN {attendance} t3 ON t2.attendanceid = t3.id
                                            INNER JOIN {attendance_statuses}  t4 ON t1.statusid = t4.id
                                            WHERE (t1.statusid =6 OR t1.statusid =7)AND t1.studentid = ? 
                                            AND course = ? AND t1.remarks = ''",array($studentid,$courseid));

        $result = array();
        $count = 0;
        foreach ($attendences as $attendance) {

            $result[$count]['id'] = $attendance->id;
            $result[$count]['time'] = date("Y-m-d", $attendance->sessdate);
            $result[$count]['description'] = $attendance->description;
            $count++;
        }

        return $result;
    }
    
    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_attendance_returns() {
       return new external_multiple_structure(
            new external_single_structure(
                array(
                    'id' => new external_value(PARAM_INT, 'Id of the attendance_log db table'),
                    'time' => new external_value(PARAM_TEXT, 'Absent or Late date time'),
                    'description' => new external_value(PARAM_TEXT, 'Absent or Late'),
                )
            )
        );
    }
    
    //==========
    
}

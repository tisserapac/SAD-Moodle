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
        $context = get_context_instance(CONTEXT_USER, $USER->id);
        self::validate_context($context);
        
        return $result;
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
    }

    public static function get_child_returns() {
        return new external_value(PARAM_TEXT, 'The welcome message + user first name');
    }

    //==========
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
                                              WHERE u.id = ?", array($studentid));

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
    //==========
    
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_grades_all_parameters() {
        return new external_function_parameters(
                array('message' => new external_value(PARAM_TEXT, 'The initial message. By default it is "All '
                                                                  . 'Grades,"', VALUE_DEFAULT, 'All '
                                                                  . 'Grades, '))
        );
    }

    /**
     * 
     * @global type $DB
     * @param type $studentid - The id of the student who's course list needs to be retrieved
     * @return type
     */
    public static function get_grades_all($studentid) {
        global $USER, $DB;
        
        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, u.firstname, u.lastname
                                              FROM {role_assignments} ra, {context} c, {user} u
                                              WHERE ra.userid = ?
                                              AND ra.contextid = c.id
                                              AND c.instanceid = u.id
                                              AND c.contextlevel = " . CONTEXT_USER, array($USER->id));

        $result =  array();
        $count = 0;
        foreach ($usercontexts as $usercontext) {
            
            $result[$count]['id'] = $usercontext->instanceid;
            $result[$count]['fullname'] = fullname($usercontext);
            
            $grades = $DB->get_records_sql("SELECT gg.finalgrade, gi.courseid, c.fullname as coursename     
                                            FROM {grade_grades} gg
                                            INNER JOIN {grade_items} gi ON gg.itemid = gi.id
                                            INNER JOIN {course} AS c ON gi.courseId = c.id
                                            WHERE gg.userid = ? ",array ($usercontext->instanceid));
            $result_grade = array();
            $count2 = 0;
            
            foreach ($grades as $grade) {
                $result_grade[$count2]['courseid'] = $grade->courseid;
                $result_grade[$count2]['coursename'] = $grade->coursename;
                $result_grade[$count2]['grade'] = $grade->finalgrade;
                $count2++;
            }
            $result[$count]['grades'] = $result_grade;
            $count++;
        }
        
        $final_result =  array();
        $final_result['grades'] = $result;
        return $final_result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_grades_all_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array ('id' => new external_value(PARAM_INT, 'Id of the student'),
                       'fullname' => new external_value(PARAM_TEXT, 'full name of student'),
                        array('cousreid' => new external_value(PARAM_TEXT, 'course id'),
                              'cousename' => new external_value(PARAM_TEXT, 'course name'),
                              'grade' => new external_value(PARAM_TEXT, 'grade')
                        )
            
                )
            )
        );
    }
    
    //==========
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
    public static function get_attendance($studentid, $courseid) {
        global $DB;

        //Need to validate the child belongs to the loged in parent

        $attendences = $DB->get_records_sql("SELECT al.id, as.sessdate, ats.description
                                            FROM {attendance_log} al
                                            INNER JOIN {attendance_sessions} as ON al.sessionid = as.id
                                            INNER JOIN {attendance} att ON as.attendanceid = att.id
                                            INNER JOIN {attendance_statuses}  ats ON al.statusid = ats.id
                                            WHERE (ats.description = 'Late' OR ats.description = 'Absent')AND al.studentid = ? 
                                            AND course = ?", array($studentid, $courseid));

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
                    array('id' => new external_value(PARAM_INT, 'Id of the attendance_log db table'),
                          'time' => new external_value(PARAM_TEXT, 'Absent or Late date time'),
                          'description' => new external_value(PARAM_TEXT, 'Absent or Late'),
                    )
                )
        );
    }

    //==========
    //==========
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_attendance_all_nested_parameters() {
        return new external_function_parameters(
                array('message' => new external_value(PARAM_TEXT, 'The initial message. By default it is "All Attendance,"', VALUE_DEFAULT, 'All Attendance, '))
        );
    }

    /**
     * This method returns the attendence details of all the students for all subjects belonging to
     * one parent in nested JSON format
     * @global type $DB
     * @param type $studentid
     * @return type
     */
    public static function get_attendance_all_nested($message = 'All Attendance') {
        global $USER, $DB;
        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, u.firstname, u.lastname
                                              FROM {role_assignments} ra, {context} c, {user} u
                                              WHERE ra.userid = ?
                                              AND ra.contextid = c.id
                                              AND c.instanceid = u.id
                                              AND c.contextlevel = " . CONTEXT_USER ." ORDER BY c.instanceid", array($USER->id));

        $result =  array();
        $count = 0;
        foreach ($usercontexts as $usercontext) {

            $result[$count] ['id'] = $usercontext->instanceid;
            $result[$count]['fullname'] = fullname($usercontext);

            $courses = $DB->get_records_sql("SELECT DISTINCT c.id, c.fullname
                                              FROM {course} AS c
                                              JOIN {context} AS ctx ON c.id = ctx.instanceid
                                              JOIN {role_assignments} AS ra ON ra.contextid = ctx.id
                                              JOIN {user} AS u ON u.id = ra.userid
                                              WHERE u.id = ? ORDER BY c.id", array($usercontext->instanceid));

            $course_result = array();
            $count_course = 0;
            foreach ($courses as $course) {

                $course_result[$count_course]['courseid'] = $course->id;
                $course_result[$count_course]['coursename'] = $course->fullname;

                $attendences = $DB->get_records_sql("SELECT at_log.id, at_sess.sessdate, at_stat.description
                                            FROM {attendance_log} at_log
                                            INNER JOIN {attendance_sessions} at_sess ON at_log.sessionid = at_sess.id
                                            INNER JOIN {attendance} attend ON at_sess.attendanceid = attend.id
                                            INNER JOIN {attendance_statuses}  at_stat ON at_log.statusid = at_stat.id
                                            WHERE (at_stat.description = 'Late' OR at_stat.description = 'Absent')AND at_log.studentid = ? 
                                            AND course = ? ORDER BY at_log.id", array($usercontext->instanceid,  $course->id));

                $attendance_result = array();
                $attendance_count = 0;
                foreach ($attendences as $attendance){

                    $attendance_result[$attendance_count]['attid'] = $attendance->id;
                    $attendance_result[$attendance_count]['date'] = date("Y-m-d", $attendance->sessdate);
                    $attendance_result[$attendance_count]['description'] = $attendance->description;
                    $attendance_count++;
                }
                $course_result[$count_course]['atendences'] = $attendance_result;
                
                $count_course++;
            }
            $result[$count]['courses'] = $course_result;

            $count++;
        }
        
        $final_result =  array();
        $final_result['children'] = $result;
        return $final_result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_attendance_all_nested_returns() {
        return new external_multiple_structure(
            new external_single_structure(
                array('id' => new external_value(PARAM_INT, 'Id of the attendance_log db table'),
                      'fullname' => new external_value(PARAM_TEXT, 'Id of the user db table'),
                       array('courseid' => new external_value(PARAM_INT, 'Id of the course db table'),
                             'coursename' => new external_value(PARAM_TEXT, 'Name of the course in the course db table'),
                             array('attid'=> new external_value(PARAM_INT, 'Id of the attendance_log db table'),
                                   'date'=> new external_value(PARAM_TEXT, 'Sesstime of the attendance_session db table'),
                                   'description' => new external_value(PARAM_TEXT, 'Desription of the attendance_status db table'),
                             )
                       )
                )
            )
        );
    }

    //==========
    //==========
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function get_attendance_all_parameters() {
        return new external_function_parameters(
                array('message' => new external_value(PARAM_TEXT, 'The initial message. By default it is "All '
                                                                  . 'Attendance non Nested,"', VALUE_DEFAULT, 'All '
                                                                  . 'Attendance non Nested, '))
        );
    }

    /**
     * This method returns the attendence details of all the students for all subjects belonging to
     * one parent in non nested JSON Format
     * @global type $DB
     * @param type $studentid
     * @return type
     */
    public static function get_attendance_all($message = 'All Attendance non Nested') {
        global $USER, $DB;
        
        $usercontexts = $DB->get_records_sql("SELECT c.instanceid, u.firstname, u.lastname
                                              FROM {role_assignments} ra, {context} c, {user} u
                                              WHERE ra.userid = ?
                                              AND ra.contextid = c.id
                                              AND c.instanceid = u.id
                                              AND c.contextlevel = " . CONTEXT_USER, array($USER->id));

        $result =  array();
        $count = 0;
        foreach ($usercontexts as $usercontext) {

            $attendences = $DB->get_records_sql("SELECT DISTINCT at_log.id, usr.id AS studentid, usr.firstname, 
                                             usr.lastname, course.fullname AS coursename, course.id AS courseid ,at_sess.sessdate, at_stat.description, at_log.remarks
                                             FROM {attendance_log} AS at_log
                                             INNER JOIN {attendance_sessions} AS at_sess  ON at_log.sessionid = at_sess.id
                                             INNER JOIN {attendance} AS attend ON at_sess.attendanceid = attend.id
                                             INNER JOIN {attendance_statuses}  AS at_stat ON at_log.statusid = at_stat.id
                                             INNER JOIN {user} AS usr ON at_log.studentid = usr.id
                                             INNER JOIN {course} AS course ON attend.course = course.id
                                             WHERE (at_stat.description = 'Late' OR at_stat.description = 'Absent')
                                             AND at_log.studentid = ? ORDER BY  usr.id, coursename, at_sess.sessdate", array($usercontext->instanceid));

            foreach ($attendences as $attendance) {

                $result[$count]['id'] = $attendance->id;
                $result[$count]['studentid'] = $attendance->studentid;
                $result[$count]['firstname'] = $attendance->firstname;
                $result[$count]['lastname'] = $attendance->lastname;
                $result[$count]['courseid'] = $attendance->courseid;
                $result[$count]['coursename'] = $attendance->coursename;            
                $result[$count]['date'] = date("Y-m-d", $attendance->sessdate);
                $result[$count]['description'] = $attendance->description;
                $count++;
            }

            $count++;
        }

        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function get_attendance_all_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                    array(
                        'id' => new external_value(PARAM_INT, 'Id of the attendance_log db table'),
                        'studentid' => new external_value(PARAM_INT, 'Id of the user db table'),
                        'firstname' => new external_value(PARAM_TEXT, 'firstname of the usr db table'),
                        'lastname' => new external_value(PARAM_TEXT, 'lastname of the user db table'),
                        'courseid' => new external_value(PARAM_TEXT, 'id of the course db table'),
                        'coursename' => new external_value(PARAM_TEXT, 'fullname of the course db table'),
                        'time' => new external_value(PARAM_TEXT, 'Absent or Late date time'),
                        'description' => new external_value(PARAM_TEXT, 'Absent or Late'),
                    )
                )
        );
    }

    //==========
    //==========
    /**
     * Returns description of method parameters
     * @return external_function_parameters
     */
    public static function update_attendance_remarks_parameters() {
        return new external_function_parameters(
                array('attendanceid' => new external_value(PARAM_INT, 'The attendance id'),
                      'remarks' => new external_value(PARAM_TEXT, 'The remark'))
        );
    }

    /**
     * This method returns the attendence details of all the students for all subjects belonging to
     * one parent in non nested JSON Format
     * @global type $DB
     * @param type $studentid
     * @return type
     */
    public static function update_attendance_remarks($attendanceid, $remarks) {
        global $DB;
        
        if ($DB->record_exists('attendance_log', array('id' => $attendanceid))) {  
            
            $result = $DB->set_field('attendance_log', 'remarks', $remarks, array('id' => $attendanceid));
        }
        
        //$record = $DB->get_records_sql("SELECT * FROM {attendance_log} WHERE id = ?",array($attendanceid));
        //print_r($record);
        return $result;
    }

    /**
     * Returns description of method result value
     * @return external_description
     */
    public static function update_attendance_remarks_returns() {
        return new external_multiple_structure(
                new external_single_structure(
                    array('result' => new external_value(PARAM_TEXT, 'Result of the attendance remarks update')
                    )
                )
        );
    }

    //==========
}

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
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// We defined the web service functions to install.
$functions = array(
    'local_wstemplate_hello_world' => array(
        'classname' => 'local_wstemplate_external',
        'methodname' => 'hello_world',
        'classpath' => 'local/wstemplate/externallib.php',
        'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
        'type' => 'read',
    ),
    'local_wstemplate_quick_test' => array(
        'classname' => 'local_wstemplate_external',
        'methodname' => 'get_child',
        'classpath' => 'local/wstemplate/externallib.php',
        'description' => 'Return Hello World FIRSTNAME. Can change the text (Hello World) sending a new text as parameter',
        'type' => 'read',
    ),
    'local_wstemplate_course_list' => array(
        'classname' => 'local_wstemplate_external',
        'methodname' => 'get_course_list',
        'classpath' => 'local/wstemplate/externallib.php',
        'description' => 'Return the list of courses enrolled by a student',
        'type' => 'read',
    )
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
    'My service' => array(
        'functions' => array('local_wstemplate_hello_world', 'local_wstemplate_quick_test','local_wstemplate_course_list'),
        'restrictedusers' => 0,
        'enabled' => 1,
    )
);

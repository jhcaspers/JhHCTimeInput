<?php
/**
 * Created by Jan-Hendrik Caspers
 * Date: 14.07.2018
 * Time: 09:41
 * Home Control (HC) Time input for setting heating, shutter or lighting times on a 24 hours base
 * This is a demonstration class how to process the data from the js control
 */

/*
 * This is the example SQL Table
 * CREATE TABLE `hc_timetable` ( `calendar_id` INT NOT NULL , `interval` INT NOT NULL , `state` BOOLEAN NOT NULL , INDEX `calid` (`calendar_id`)) ENGINE = MyISAM COMMENT = 'Test Table to store HC Time';
 * */
namespace jhhctimeinput;


/**
 * Class hctimeinput
 * @package jhhctimeinput
 */
class hctimeinput
{
    private $_mysqli;
    private $_data;
    private $_calendarId;
    private $_numberOfIntervals=96;
    /**
     * hctimeinput constructor.
     * @param $calendarId
     * @param int $numberOfIntervals
     */
    function __construct($calendarId,$numberOfIntervals=96) {
        $this->_initializeEmptyCalendar();
        // This is only for testing, you should configure the server in a central config file
        $this->_mysqli = new \mysqli("localhost", "YourDBUser", "YourPasswod", "YourDatabase");
        // Just die in case of an error
        if ($this->_mysqli->connect_error) {
            die('Connect Error (' . $this->_mysqli->connect_errno . ') '
                . $this->_mysqli->connect_error);
        }
        $this->_calendarId=(int)$calendarId;
        $this->loadData();

        $this->_numberOfIntervals=(int)$numberOfIntervals;
    }

    /**
     * loads the Calendar into the object
     */
    private function loadData() {

        if ($stmt =  $this->_mysqli->prepare("select * from hc_timetable where calendar_id = ?")) {
            // bind parameters
            $stmt->bind_param("i", $this->_calendarId);
            $stmt->execute();
            // fetch result
            $res=$stmt->get_result();
            if ($res) {
                while ($row = $res->fetch_assoc()) {
                    $this->_data[$row['interval']]=$row['state'];
                }
            } else $this->_initializeEmptyCalendar();
            $stmt->close();
        }
    }

    /**
     * initialize the data array
     */
    private function _initializeEmptyCalendar()
    {
        for($i=0;$i<$this->_numberOfIntervals;$i++) {
            $this->_data[$i]=0;
        }
    }

    /**
     * Return the values from the data array as comma separated list to be used as an js array
     * @return string
     */
    function GetJSDataArray() {
        return implode(",",$this->_data);
    }

    /**
     * @param $interval
     * @param $value
     * @return bool
     */
    function UpdateInterval($interval, $value){
        $this->_data[$interval]=($value==1?1:0);
        return $this->_saveInterval($interval);
    }

    /**
     * @param $interval
     * @return bool
     */
    private function _saveInterval($interval)
    {
        $new=false;
        if ($stmt =  $this->_mysqli->prepare("update hc_timetable set state = ? where calendar_id = ? and `interval`=?")) {
            // bind parameters
            $stmt->bind_param("iii", $this->_data[$interval], $this->_calendarId,$interval);
            $stmt->execute();
            if ($stmt->affected_rows!=1) $new=true;
            $stmt->close();
        }
        if($new) {
            if ($stmt =  $this->_mysqli->prepare("insert into hc_timetable (`calendar_id`, `interval`, `state`) values (?,?,?)")) {
                // bind parameters
                $stmt->bind_param("iii", $this->_calendarId, $interval, $this->_data[$interval]);
                $stmt->execute();
                $stmt->close();
            }
        }
        return true;
    }

    /**
     * get the CalendarId
     * @return int
     */
    function getId() {
        return $this->_calendarId;
    }
}
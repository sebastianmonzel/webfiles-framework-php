<?php

namespace simpleserv\webfilesframework\core\time;

/**
 * description
 *
 * @author     simpleserv company < info@simpleserv.de >
 * @author     Sebastian Monzel < mail@sebastianmonzel.de >
 * @since      0.1.7
 */
class MTimestampHelper
{

    public static $m__sClassName = __CLASS__;

    public static function getMonthStart($month = -1, $year = -1)
    {
        if ($month == -1) {
            $month = date("n");
        }
        if ($year == -1) {
            $year = date("Y");
        }
        return mktime(0, 0, 0, $month, 1, $year);
    }

    /**
     * returns a timestamp of the end of a month
     *
     */
    public static function getMonthEnd($month = -1, $year = -1)
    {
        if ($month == -1) {
            $month = date("n");
        }
        if ($year == -1) {
            $year = date("Y");
        }

        //gets the number of days in the actual month
        $tempTime = mktime(0, 0, 0, $month, 1, $year);
        $tempTime = date("t", $tempTime);

        return mktime(23, 59, 59, $month, $tempTime, $year);
    }


    /**
     * returns the count of hours in the given duration
     * @param duration duration in seconds
     */
    public static function getHours($duration)
    {
        $duration = $duration % (24 * 3600);
        return floor($duration / 3600);

    }


    /**
     * returns the count of minutes in the given duration
     * @param duration duration in seconds
     */
    public static function getMinutes($duration)
    {
        $duration = $duration % 3600;
        return floor($duration / 60);
    }

    /**
     * returns the number of the day
     */
    public static function getDay($timestamp, $leadingZero = true)
    {
        if ($leadingZero) {
            return date("d", $timestamp);
        } else {
            return date("j", $timestamp);
        }
    }


    /**
     * returns the number of the month
     */
    public static function getMonth($timestamp, $leadingZero = true)
    {
        if ($leadingZero) {
            return date("m", $timestamp);
        } else {
            return date("n", $timestamp);
        }
    }

    /**
     * returns the number of the year
     */
    public static function getYear($timestamp)
    {
        return date("Y", $timestamp);
    }


    /**
     * returns the number of the year
     */
    public static function getHour($timestamp)
    {
        return date("H", $timestamp);
    }


    /**
     * returns the number of the year
     */
    public static function getMinute($timestamp)
    {
        return date("i", $timestamp);
    }

    /**
     * returns the given timestamp in a manner like
     * @param duration duration in seconds
     */
    public static function getFormatedDate($timestamp)
    {
        return MTimestampHelper::getDay($timestamp) . "." . MTimestampHelper::getMonth($timestamp) . "." . MTimestampHelper::getYear($timestamp);
    }


    /**
     * returns the count of minutes in the given duration
     * @param duration duration in seconds
     */
    public static function getFormatedTime($timestamp)
    {
        return MTimestampHelper::getHour($timestamp) . ":" . MTimestampHelper::getMinute($timestamp);
    }

    public static function getWeekdayName($identifier)
    {
        switch ($identifier) {
            case 0:
                return "Sonntag";
                break;

            case 1:
                return "Montag";
                break;

            case 2:
                return "Dienstag";
                break;

            case 3:
                return "Mittwoch";
                break;

            case 4:
                return "Donnerstag";
                break;

            case 5:
                return "Freitag";
                break;

            case 6:
                return "Samstag";
                break;

            default:
                ;
                break;
        }
    }

    public static function getDojoFormatedDate($timestamp)
    {

        $day = MTimestampHelper::getDay(intval($timestamp));
        $month = MTimestampHelper::getMonth(intval($timestamp));
        $year = MTimestampHelper::getYear(intval($timestamp));

        return $year . "-" . $month . "-" . $day;
    }

    public static function getDojoFormatedTime($timestamp)
    {
        $hour = MTimestampHelper::getHour(intval($timestamp));
        $minute = MTimestampHelper::getMinute(intval($timestamp));

        return "T" . $hour . ":" . $minute . ":00";
    }

    public static function getTimestampFromDojoFromatedDate($dojoDate)
    {
        $dojoDate = explode("-", $dojoDate);

        if (count($dojoDate) == 3) {
            $day = intval($dojoDate[2]);
            $month = intval($dojoDate[1]);
            $year = intval($dojoDate[0]);

            $timestamp = mktime('0', '0', '0', $month, $day, $year);
        } else {
            $timestamp = mktime('0', '0', '0', '0', '0', '0');
        }


        return $timestamp;
    }

    public static function getTimestampFromDojoFromatedDateTime($dojoDate, $dojoTime)
    {

        $dojoDate = explode("-", $dojoDate);

        $day = intval($dojoDate[2]);
        $month = intval($dojoDate[1]);
        $year = intval($dojoDate[0]);

        $hour = substr($dojoTime, 1, 2);;
        $minute = substr($dojoTime, 4, 2);;

        $timestamp = mktime($hour, $minute, '0', $month, $day, $year);

        return $timestamp;
    }

}
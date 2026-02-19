<?php

use test\webfilesframework\MAbstractWebfilesFramworkTest;
use webfilesframework\core\datatype\time\MTimestampHelper;
use webfilesframework\MWebfilesFrameworkException;

/**
 * @covers webfilesframework\core\datatype\time\MTimestampHelper
 */
class MTimestampHelperTest extends MAbstractWebfilesFramworkTest {

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getMonthStart
     */
    public function testGetMonthStart() {
        // Test January 2024
        $timestamp = MTimestampHelper::getMonthStart(1, 2024);
        $this->assertEquals('2024-01-01', date('Y-m-d', $timestamp));
        $this->assertEquals('00:00:00', date('H:i:s', $timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getMonthEnd
     */
    public function testGetMonthEnd() {
        // Test January 2024 (31 days)
        $timestamp = MTimestampHelper::getMonthEnd(1, 2024);
        $this->assertEquals('2024-01-31', date('Y-m-d', $timestamp));
        $this->assertEquals('23:59:59', date('H:i:s', $timestamp));
        
        // Test February 2024 (29 days - leap year)
        $timestamp = MTimestampHelper::getMonthEnd(2, 2024);
        $this->assertEquals('2024-02-29', date('Y-m-d', $timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getHours
     */
    public function testGetHours() {
        // 1 day, 5 hours, 30 minutes = 86400 + 19800 + 1800 = 108000 seconds
        // Should return 5 (hours in the day part)
        $duration = 5 * 3600 + 30 * 60;
        $this->assertEquals(5, MTimestampHelper::getHours($duration));
        
        // Full day (24 hours) should return 0
        $this->assertEquals(0, MTimestampHelper::getHours(24 * 3600));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getMinutes
     */
    public function testGetMinutes() {
        // 2 hours, 45 minutes = 7200 + 2700 = 9900 seconds
        // Should return 45
        $duration = 2 * 3600 + 45 * 60;
        $this->assertEquals(45, MTimestampHelper::getMinutes($duration));
        
        // Just seconds should return 0
        $this->assertEquals(0, MTimestampHelper::getMinutes(30));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getDay
     */
    public function testGetDay() {
        $timestamp = mktime(0, 0, 0, 3, 5, 2024);
        $this->assertEquals('05', MTimestampHelper::getDay($timestamp, true));
        $this->assertEquals('5', MTimestampHelper::getDay($timestamp, false));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getMonth
     */
    public function testGetMonth() {
        $timestamp = mktime(0, 0, 0, 3, 5, 2024);
        $this->assertEquals('03', MTimestampHelper::getMonth($timestamp, true));
        $this->assertEquals('3', MTimestampHelper::getMonth($timestamp, false));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getYear
     */
    public function testGetYear() {
        $timestamp = mktime(0, 0, 0, 3, 5, 2024);
        $this->assertEquals('2024', MTimestampHelper::getYear($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getHour
     */
    public function testGetHour() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('14', MTimestampHelper::getHour($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getMinute
     */
    public function testGetMinute() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('30', MTimestampHelper::getMinute($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getFormatedDate
     */
    public function testGetFormatedDate() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('05.03.2024', MTimestampHelper::getFormatedDate($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getFormatedTime
     */
    public function testGetFormatedTime() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('14:30', MTimestampHelper::getFormatedTime($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getWeekdayName
     */
    public function testGetWeekdayName() {
        $this->assertEquals('Sonntag', MTimestampHelper::getWeekdayName(0));
        $this->assertEquals('Montag', MTimestampHelper::getWeekdayName(1));
        $this->assertEquals('Dienstag', MTimestampHelper::getWeekdayName(2));
        $this->assertEquals('Mittwoch', MTimestampHelper::getWeekdayName(3));
        $this->assertEquals('Donnerstag', MTimestampHelper::getWeekdayName(4));
        $this->assertEquals('Freitag', MTimestampHelper::getWeekdayName(5));
        $this->assertEquals('Samstag', MTimestampHelper::getWeekdayName(6));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getWeekdayName
     */
    public function testGetWeekdayNameThrowsException() {
        $this->expectException(MWebfilesFrameworkException::class);
        $this->expectExceptionMessage('Unknown identifier');
        
        MTimestampHelper::getWeekdayName(7);
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getDojoFormatedDate
     */
    public function testGetDojoFormatedDate() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('2024-03-05', MTimestampHelper::getDojoFormatedDate($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getDojoFormatedTime
     */
    public function testGetDojoFormatedTime() {
        $timestamp = mktime(14, 30, 0, 3, 5, 2024);
        $this->assertEquals('T14:30:00', MTimestampHelper::getDojoFormatedTime($timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getTimestampFromDojoFromatedDate
     */
    public function testGetTimestampFromDojoFromatedDate() {
        $dojoDate = '2024-03-05';
        $timestamp = MTimestampHelper::getTimestampFromDojoFromatedDate($dojoDate);
        $this->assertEquals('2024-03-05', date('Y-m-d', $timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getTimestampFromDojoFromatedDateTime
     */
    public function testGetTimestampFromDojoFromatedDateTime() {
        $dojoDate = '2024-03-05';
        $dojoTime = 'T14:30:00';
        $timestamp = MTimestampHelper::getTimestampFromDojoFromatedDateTime($dojoDate, $dojoTime);
        
        $this->assertEquals('2024-03-05', date('Y-m-d', $timestamp));
        $this->assertEquals('14:30', date('H:i', $timestamp));
    }

    /**
     * @covers webfilesframework\core\datatype\time\MTimestampHelper::getTimestampFromExifFormatedDateTime
     */
    public function testGetTimestampFromExifFormatedDateTime() {
        $exifDateTime = '2024:03:05 14:30:45';
        $timestamp = MTimestampHelper::getTimestampFromExifFormatedDateTime($exifDateTime);
        
        $this->assertEquals('2024-03-05', date('Y-m-d', $timestamp));
        $this->assertEquals('14:30:45', date('H:i:s', $timestamp));
    }
}

<?php

namespace Nuxia\BusinessDayManipulator\tests;

use Nuxia\BusinessDayManipulator\DatePeriod;
use Nuxia\BusinessDayManipulator\Manipulator;

class ManipulatorTest extends \PHPUnit_Framework_TestCase
{
    public function testSetStartDate()
    {
        $manipulator = new Manipulator();

        $startDate = new \DateTime('2015-04-01');

        $manipulator->setStartDate($startDate);

        $this->assertEquals($startDate, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'startDate'));
        $this->assertEquals($startDate, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'cursorDate'));
    }

    public function testSetHolidays()
    {
        $holidays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
            new \DateTime('2015-04-02'),
        ];

        $expected = [
            new \DateTime('2015-04-07'),
            new \DateTime('2015-04-08'),
            new \DateTime('2015-04-09'),
            new \DateTime('2015-04-10'),
            new \DateTime('2015-04-11'),
            new \DateTime('2015-04-12'),
            new \DateTime('2015-04-13'),
            new \DateTime('2015-04-14'),
            new \DateTime('2015-04-02'),
        ];

        $manipulator = new Manipulator([], [], $holidays);

        $this->assertEquals($expected, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'holidays'));
    }

    public function testAddHoliday()
    {
        $manipulator = new Manipulator();

        $manipulator->addHoliday(new DatePeriod(new \DateTime('2015-04-01'), new \DateTime('2015-04-03')));
        $manipulator->addHoliday(new \DateTime('2015-04-04'));

        $expected = [
            new \DateTime('2015-04-01'),
            new \DateTime('2015-04-02'),
            new \DateTime('2015-04-03'),
            new \DateTime('2015-04-04'),
        ];

        $this->assertEquals($expected, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'holidays'));
    }

    public function testSetFreeDays()
    {
        $freeDays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
            new \DateTime('2015-04-02'),
        ];

        $expected = [
            new \DateTime('2015-04-07'),
            new \DateTime('2015-04-08'),
            new \DateTime('2015-04-09'),
            new \DateTime('2015-04-10'),
            new \DateTime('2015-04-11'),
            new \DateTime('2015-04-12'),
            new \DateTime('2015-04-13'),
            new \DateTime('2015-04-14'),
            new \DateTime('2015-04-02'),
        ];

        $manipulator = new Manipulator($freeDays, [], []);

        $this->assertEquals($expected, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'freeDays'));
    }

    public function testAddFreeDay()
    {
        $manipulator = new Manipulator();

        $manipulator->addFreeDay(new DatePeriod(new \DateTime('2015-04-01'), new \DateTime('2015-04-03')));
        $manipulator->addFreeDay(new \DateTime('2015-04-04'));

        $expected = [
            new \DateTime('2015-04-01'),
            new \DateTime('2015-04-02'),
            new \DateTime('2015-04-03'),
            new \DateTime('2015-04-04'),
        ];

        $this->assertEquals($expected, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'freeDays'));
    }

    public function testSetFeeWeekDays()
    {
        $freeWeekDays = [
            Manipulator::SATURDAY,
            Manipulator::SUNDAY,
        ];

        $manipulator = new Manipulator([], $freeWeekDays, []);

        $this->assertEquals($freeWeekDays, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'freeWeekDays'));
    }

    public function testAddFreeWeekDays()
    {
        $manipulator = new Manipulator();

        $manipulator->addFreeWeekDays(Manipulator::SATURDAY);

        $this->assertEquals([Manipulator::SATURDAY], \PHPUnit_Framework_Assert::readAttribute($manipulator, 'freeWeekDays'));
    }

    public function testAddBusinessDays()
    {
        $manipulator = new Manipulator([], [], []);

        $manipulator->setStartDate(new \DateTime());
        $manipulator->addBusinessDays(5);

        $this->assertEquals(new \DateTime('now + 5 days'), $manipulator->getDate());
    }

    public function testIsBusinessDay()
    {
        $freeWeeksDay = [
            Manipulator::SATURDAY,
            Manipulator::SUNDAY,
        ];

        $freeDays = [
            new \DateTime('2015-04-01'),
        ];

        $holidays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
        ];

        $manipulator = new Manipulator($freeDays, $freeWeeksDay, $holidays);

        $this->assertTrue($manipulator->isBusinessDay(new \DateTime('2015-04-02')));
        $this->assertTrue($manipulator->isBusinessDay(new \DateTime('2015-04-03')));
        $this->assertFalse($manipulator->isBusinessDay(new \DateTime('2015-04-04')));
        $this->assertFalse($manipulator->isBusinessDay(new \DateTime('2015-04-05')));
        $this->assertTrue($manipulator->isBusinessDay(new \DateTime('2015-04-06')));
        $this->assertFalse($manipulator->isBusinessDay(new \DateTime('2015-04-07')));
        $this->assertFalse($manipulator->isBusinessDay(new \DateTime('2015-04-08')));
        $this->assertTrue($manipulator->isBusinessDay(new \DateTime('2015-04-15')));
    }

    public function testGetTypeOfDay()
    {
        $freeWeeksDay = [
            Manipulator::SATURDAY,
            Manipulator::SUNDAY,
        ];

        $freeDays = [
            new \DateTime('2015-04-01'),
        ];

        $holidays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
        ];

        $manipulator = new Manipulator($freeDays, $freeWeeksDay, $holidays);

        $this->assertNotEquals(Manipulator::TOD_FREE_WEEK_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-1')));
        $this->assertEquals(Manipulator::TOD_HOLIDAY_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-08')));
        $this->assertEquals(Manipulator::TOD_HOLIDAY_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-09')));
        $this->assertEquals(Manipulator::TOD_HOLIDAY_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-10')));

        $this->assertNotEquals(Manipulator::TOD_FREE_WEEK_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-1')));
        $this->assertEquals(Manipulator::TOD_FREE_WEEK_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-18')));
        $this->assertEquals(Manipulator::TOD_FREE_WEEK_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-19')));

        $this->assertEquals(Manipulator::TOD_WORKING_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-02')));
        $this->assertEquals(Manipulator::TOD_WORKING_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-03')));
        $this->assertNotEquals(Manipulator::TOD_WORKING_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-13')));

        $this->assertEquals(Manipulator::TOD_FREE_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-01')));
        $this->assertNotEquals(Manipulator::TOD_FREE_DAY, $manipulator->getTypeOfDay(new \DateTime('2015-04-12')));
    }

    public function testSetEndDate()
    {
        $manipulator = new Manipulator();

        $manipulator->setStartDate(new \DateTime());

        try {
            $manipulator->setEndDate(new \DateTime('now - 1 month'));
        } catch (\LogicException $e) {
            $this->assertEquals('endDate must after your starting date', $e->getMessage());
        }

        $endDate = new \DateTime('now + 1 month');
        $unreferencedDate = $endDate->format('Y-m-d h:i:s');

        $manipulator->setEndDate($endDate);

        $this->assertEquals($endDate, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'endDate'));

        //Check the interval date is not passed by reference. When we modify the original object, we don't want modify the date passed in Manipulator
        $endDate->add(new \DateInterval('P1M'));
        $this->assertEquals($unreferencedDate, \PHPUnit_Framework_Assert::readAttribute($manipulator, 'endDate')->format('Y-m-d h:i:s'));
    }

    public function testGetBusinessDays()
    {
        $freeWeeksDay = [
            Manipulator::SATURDAY,
            Manipulator::SUNDAY,
        ];

        $freeDays = [
            new \DateTime('2015-04-01'),
        ];

        $holidays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
        ];

        $manipulator = new Manipulator($freeDays, $freeWeeksDay, $holidays);

        $manipulator->setStartDate(new \DateTime('2015-04-01'));

        $manipulator->setEndDate(new \DateTime('2015-04-15'));
        $this->assertEquals(4, $manipulator->getBusinessDays());

        $manipulator->setEndDate(new \DateTime('2015-04-30'));
        $this->assertEquals(15, $manipulator->getBusinessDays());

        $manipulator->setEndDate(new \DateTime('2015-04-03'));
        $this->assertEquals(2, $manipulator->getBusinessDays());

        $manipulator->setStartDate(new \DateTime('2015-04-23'));
        $manipulator->setEndDate(new \DateTime('2015-04-31'));
        $this->assertEquals(7, $manipulator->getBusinessDays());
    }

    public function testGetBusinessDaysDate()
    {
        $freeWeeksDay = [
            Manipulator::SATURDAY,
            Manipulator::SUNDAY,
        ];

        $freeDays = [
            new \DateTime('2015-04-01'),
        ];

        $holidays = [
            new DatePeriod(new \DateTime('2015-04-07'), new \DateTime('2015-04-14')),
        ];

        $manipulator = new Manipulator($freeDays, $freeWeeksDay, $holidays);

        $manipulator->setStartDate(new \DateTime('2015-04-01'));

        $manipulator->setEndDate(new \DateTime('2015-04-15'));

        $this->assertEquals([
            new \DateTime('2015-04-02'),
            new \DateTime('2015-04-03'),
            new \DateTime('2015-04-06'),
            new \DateTime('2015-04-15'),
        ], $manipulator->getBusinessDaysDate());

        $manipulator->setEndDate(new \DateTime('2015-04-30'));
        $this->assertEquals([
            new \DateTime('2015-04-02'),
            new \DateTime('2015-04-03'),
            new \DateTime('2015-04-06'),
            new \DateTime('2015-04-15'),
            new \DateTime('2015-04-16'),
            new \DateTime('2015-04-17'),
            new \DateTime('2015-04-20'),
            new \DateTime('2015-04-21'),
            new \DateTime('2015-04-22'),
            new \DateTime('2015-04-23'),
            new \DateTime('2015-04-24'),
            new \DateTime('2015-04-27'),
            new \DateTime('2015-04-28'),
            new \DateTime('2015-04-29'),
            new \DateTime('2015-04-30'),
        ], $manipulator->getBusinessDaysDate());

        $manipulator->setEndDate(new \DateTime('2015-04-03'));
        $this->assertEquals([
            new \DateTime('2015-04-02'),
            new \DateTime('2015-04-03'),
        ], $manipulator->getBusinessDaysDate());

        $manipulator->setStartDate(new \DateTime('2015-04-23'));
        $manipulator->setEndDate(new \DateTime('2015-04-31'));

        $this->assertEquals([
            new \DateTime('2015-04-23'),
            new \DateTime('2015-04-24'),
            new \DateTime('2015-04-27'),
            new \DateTime('2015-04-28'),
            new \DateTime('2015-04-29'),
            new \DateTime('2015-04-30'),
            new \DateTime('2015-04-31'),
        ], $manipulator->getBusinessDaysDate());
    }
}

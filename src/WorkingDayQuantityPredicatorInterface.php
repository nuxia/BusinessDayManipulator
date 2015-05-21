<?php

namespace Nuxia\BusinessDayManipulator;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
interface WorkingDayQuantityPredicatorInterface
{
    /**
     * @param \DateTime $startDate Date to start calculations from
     *
     * @return $this
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * @param \DateTime $date
     */
    public function setEndDate(\DateTime $date);

    /**
     * @return int
     */
    public function getBusinessDays();

    /**
     * @return \DateTime
     */
    public function getBusinessDaysDate();

    /**
     * @param int $freeWeekDay
     *
     * @throws \Exception
     */
    public function addFreeWeekDays($freeWeekDay);

    /**
     * @param \DateTime|DatePeriod $freeDay
     *
     * @return $this
     */
    public function addFreeDay($freeDay);

    /**
     * @param \DateTime|DatePeriod $holiday
     *
     * @return $this
     */
    public function addHoliday($holiday);

    /**
     * @return \DateTime[]
     */
    public function getNonWorkingDays();
}

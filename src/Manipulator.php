<?php

namespace Nuxia\BusinessDayManipulator;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class Manipulator implements ManipulatorInterface
{
    const WEEK_DAY_FORMAT = 'N';
    const HOLIDAY_FORMAT  = 'm-d';
    const FREE_DAY_FORMAT = 'Y-m-d';

    /** @var \DateTime */
    protected $startDate;

    /** @var  \DateTime */
    protected $endDate;

    /** @var  \DateTime */
    protected $cursorDate;

    /** @var \DateTime[] */
    protected $holidays = [];

    /** @var \DateTime[] */
    protected $freeDays = [];

    /** @var int[] */
    protected $freeWeekDays = [];

    /**
     * @param \DateTime[]|DatePeriod[] $freeDays
     * @param int[]                    $freeWeekDays
     * @param \DateTime[]|DatePeriod[] $holidays
     */
    public function __construct(Array $freeDays = array(), Array $freeWeekDays = array(), Array $holidays = array())
    {
        $this->startDate = new \DateTime();
        $this->cursorDate = new \DateTime();

        $this->setHolidays($holidays);
        $this->setFreeDays($freeDays);
        $this->setFreeWeekDays($freeWeekDays);
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate(\DateTime $startDate)
    {
        //Clone to break object referencing
        $this->startDate =  clone $startDate;
        $this->cursorDate = clone $startDate; //if we set again new date, adjust the cursor to her.

        return $this;
    }

    /**
     * @param \DateTime[] $holidays Array of holidays that repeats each year. (Only month and date is used to match).
     *
     * @return $this
     */
    protected function setHolidays(array $holidays)
    {
        foreach ($holidays as $holiday) {
            $this->addHoliday($holiday);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addHoliday($holiday)
    {
        if (!$holiday instanceof DatePeriod) {
            $this->holidays[] = $holiday;

            return $this;
        }

        foreach ($holiday->getDates() as $date) {
            $this->holidays[] = $date;
        }

        return $this;
    }

    /**
     * @return \DateTime[]
     */
    protected function getHolidays()
    {
        return $this->holidays;
    }

    /**
     * @param \DateTime[] $freeDays Array of free days that dose not repeat.
     *
     * @return $this
     */
    protected function setFreeDays(array $freeDays)
    {
        foreach ($freeDays as $freeDay) {
            if ($freeDay instanceof DatePeriod) {
                foreach ($freeDay->getDates() as $date) {
                    $this->freeDays[] = $date;
                }
            } else {
                $this->freeDays[] = $freeDay;
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFreeDay($freeDay)
    {
        if (!$freeDay instanceof DatePeriod) {
            $this->freeDays[] = $freeDay;

            return $this;
        }

        foreach ($freeDay->getDates() as $date) {
            $this->freeDays[] = $date;
        }

        return $this;
    }

    /**
     * @return \DateTime[]
     */
    protected function getFreeDays()
    {
        return $this->freeDays;
    }

    /**
     * @param int[] $freeWeekDays Array of days of the week which are not business days.
     *
     * @return $this
     */
    protected function setFreeWeekDays(array $freeWeekDays)
    {
        $this->freeWeekDays = $freeWeekDays;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFreeWeekDays($freeWeekDay)
    {
        if (array_search($freeWeekDay, $this->freeWeekDays)) {
            throw new \Exception('Already assigned');
        }

        $this->freeWeekDays[] = $freeWeekDay;

        return $this;
    }

    /**
     * @return int[]
     */
    protected function getFreeWeekDays()
    {
        if (count($this->freeWeekDays) >= 7) {
            throw new \InvalidArgumentException('Too many non business days provided');
        }

        return $this->freeWeekDays;
    }

    /**
     * {@inheritdoc}
     */
    public function addBusinessDays($howManyDays, $strategy = self::EXCLUDE_TODAY)
    {
        $today = new \DateTime();

        if ($today->format('Y-m-d') === $this->cursorDate->format('Y-m-d')) {
            if (static::EXCLUDE_TODAY === $strategy) {
                $iterator = -1;
            } elseif (static::INCLUDE_TODAY === $strategy) {
                $iterator = 0;
            } else {
                throw new \Exception('undefined strategy');
            }
        } else {
            $iterator = 0;
        }

        while ($iterator < $howManyDays) {
            if ($this->isBusinessDay($this->cursorDate)) {
                $iterator++;
            }

            if ($iterator < $howManyDays) { //Do not modify the date if we are on the last iteration
                $this->cursorDate->modify('+1 day');
            }
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->cursorDate;
    }

    /**
     * {@inheritdoc}
     */
    public function isBusinessDay(\DateTime $date)
    {
        if ($this->isHoliday($date)) {
            return false;
        }

        if ($this->isFreeWeekDayDay($date)) {
            return false;
        }

        if ($this->isFreeDay($date)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfDay(\DateTime $date)
    {
        if ($this->isHoliday($date)) {
            return static::TOD_HOLIDAY_DAY;
        }

        if ($this->isFreeWeekDayDay($date)) {
            return static::TOD_FREE_WEEK_DAY;
        }

        if ($this->isFreeDay($date)) {
            return static::TOD_FREE_DAY;
        }

        return static::TOD_WORKING_DAY;
    }

    /**
     * {@inheritdoc}
     */
    protected function isFreeWeekDayDay(\DateTime $date)
    {
        $currentWeekDay = (int) $date->format(self::WEEK_DAY_FORMAT);

        if (in_array($currentWeekDay, $this->getFreeWeekDays())) {
            return true;
        }

        return false;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    protected function isHoliday(\DateTime $date)
    {
        $holidayFormatValue = $date->format(self::HOLIDAY_FORMAT);
        foreach ($this->getHolidays() as $holiday) {
            if ($holidayFormatValue == $holiday->format(self::HOLIDAY_FORMAT)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    protected function isFreeDay(\DateTime $date)
    {
        $freeDayFormatValue = $date->format(self::FREE_DAY_FORMAT);
        foreach ($this->getFreeDays() as $freeDay) {
            if ($freeDayFormatValue == $freeDay->format(self::FREE_DAY_FORMAT)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate(\DateTime $endDate)
    {
        if ($endDate < $this->startDate) {
            throw new \LogicException('endDate must after your starting date');
        }

        $this->endDate = clone $endDate; //Break reference

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessDaysDate()
    {
        $dates = [];

        $date = clone $this->startDate; //Break reference

        $iteration = 0;

        while ($date < $this->endDate) {
            if (0 != $iteration) {
                $date->modify('+1 day');
            }

            if ($this->isBusinessDay($date)) {
                $dates[] = clone $date;
            }

            $iteration++;
        }

        return $dates;
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessDays()
    {
        return count($this->getBusinessDaysDate());
    }
}

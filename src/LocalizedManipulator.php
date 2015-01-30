<?php

namespace Nuxia\BusinessDayManipulator\src;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class LocalizedManipulator implements ManipulatorInterface
{
    /**
     * @var string
     */
    protected $tz;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var ManipulatorInterface
     */
    protected $manipulator;

    /**
     * @var array
     */
    protected $intlMapping = [];

    /**
     * @param string                   $tz
     * @param string                   $locale
     * @param \DateTime[]|DatePeriod[] $freeDays
     * @param int[]                    $freeWeekDays
     * @param \DateTime[]|DatePeriod[] $holidays
     *
     * @throws \Exception
     */
    public function __construct($tz, $locale, Array $freeDays, Array $freeWeekDays = null, Array $holidays)
    {
        if (false === extension_loaded('intl')) {
            throw new \Exception('Intl is not available');
        }

        $this->intlMapping = [
            \IntlCalendar::DOW_MONDAY => Manipulator::MONDAY,
            \IntlCalendar::DOW_TUESDAY => Manipulator::TUESDAY,
            \IntlCalendar::DOW_WEDNESDAY => Manipulator::WEDNESDAY,
            \IntlCalendar::DOW_THURSDAY => Manipulator::THURSDAY,
            \IntlCalendar::DOW_FRIDAY => Manipulator::FRIDAY,
            \IntlCalendar::DOW_SATURDAY => Manipulator::SATURDAY,
            \IntlCalendar::DOW_SUNDAY => Manipulator::SUNDAY,
        ];

        $this->tz = $tz;
        $this->locale = $locale;

        $this->manipulator = new Manipulator(
            $freeDays,
            null === $freeWeekDays ? $this->getFreeWeekDays() : $freeWeekDays,
            $holidays
        );
    }

    /**
     * @return array
     */
    protected function getFreeWeekDays()
    {
        $freeWeekDays = [];
        $calendar = \IntlCalendar::createInstance($this->tz, $this->locale);

        foreach ($this->intlMapping as $intlDay => $manipulatorDay) {
            if (1 === $calendar->getDayOfWeekType($intlDay)) {
                $freeWeekDays[] = $manipulatorDay;
            }
        }

        return $freeWeekDays;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartDate(\DateTime $startDate)
    {
        $this->manipulator->setStartDate($startDate);
    }

    /**
     * {@inheritdoc}
     */
    public function addHoliday($holiday)
    {
        $this->manipulator->addHoliday($holiday);
    }

    /**
     * {@inheritdoc}
     */
    public function addFreeDay($freeDay)
    {
        $this->manipulator->addFreeDay($freeDay);
    }

    /**
     * {@inheritdoc}
     */
    public function addFreeWeekDays($freeWeekDay)
    {
        return $this->manipulator->addFreeWeekDays($freeWeekDay);
    }

    /**
     * {@inheritdoc}
     */
    public function addBusinessDays($howManyDays, $strategy = Manipulator::EXCLUDE_TODAY)
    {
        return $this->manipulator->addBusinessDays($howManyDays, $strategy);
    }

    /**
     * {@inheritdoc}
     */
    public function getDate()
    {
        return $this->manipulator->getDate();
    }

    /**
     * {@inheritdoc}
     */
    public function isBusinessDay(\DateTime $date)
    {
        return $this->manipulator->isBusinessDay($date);
    }

    /**
     * {@inheritdoc}
     */
    public function setEndDate(\DateTime $date)
    {
        $this->manipulator->setEndDate($date);
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessDays()
    {
        return $this->manipulator->getBusinessDays();
    }

    /**
     * {@inheritdoc}
     */
    public function getBusinessDaysDate()
    {
        return $this->manipulator->getBusinessDaysDate();
    }

    /**
     * {@inheritdoc}
     */
    public function getTypeOfDay(\DateTime $date)
    {
        return $this->manipulator->getTypeOfDay($date);
    }
}

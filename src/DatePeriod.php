<?php

namespace Nuxia\BusinessDayManipulator;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class DatePeriod
{
    /**
     * @var \DateTime[]
     */
    protected $dates;

    /**
     * @param \DateTime $begin
     * @param \DateTime $end
     */
    public function __construct(\DateTime $begin, \DateTime $end)
    {
        /** @var \DatePeriod $period */
        $period = new \DatePeriod(
            $begin,
            \DateInterval::createFromDateString('1 day'),
            $end
        );

        /** @var \DateTime $dt */
        foreach ($period as $dt) {
            $this->dates[] = $dt;
        }

        $this->dates[] = $period->end;
    }

    /**
     * @return \DateTime[]
     */
    public function getDates()
    {
        return $this->dates;
    }
}

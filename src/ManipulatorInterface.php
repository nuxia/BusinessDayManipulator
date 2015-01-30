<?php

namespace Nuxia\BusinessDayManipulator\src;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
interface ManipulatorInterface extends
    WorkingDayDatePredicatorInterface,
    WorkingDayQuantityPredicatorInterface
{
    const MONDAY    = 1;
    const TUESDAY   = 2;
    const WEDNESDAY = 3;
    const THURSDAY  = 4;
    const FRIDAY    = 5;
    const SATURDAY  = 6;
    const SUNDAY    = 7;

    const EXCLUDE_TODAY = 'exclude_today';
    const INCLUDE_TODAY = 'include_today';

    const TOD_FREE_DAY = 'free_day';
    const TOD_HOLIDAY_DAY = 'holiday';
    const TOD_FREE_WEEK_DAY = 'free_week_day';
    const TOD_WORKING_DAY = 'working_day';

    /**
     * @param \DateTime $date
     *
     * @return bool
     */
    public function isBusinessDay(\DateTime $date);

    /**
     * @param \DateTime $date
     *
     * @return string
     */
    public function getTypeOfDay(\DateTime $date);
}

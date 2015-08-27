<?php

namespace Symfonian\Indonesia\CoreBundle\Toolkit\Util\DateUtil;

use DateTime;

final class ValidIntervalChecker
{
    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     * @return bool
     */
    public static function isValid(DateTime $startDate, DateTime $endDate)
    {
        if ($startDate <= $endDate) {
            return true;
        }

        return false;
    }
}
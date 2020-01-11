<?php

namespace Framework;

class Calendar
{

    public function __construct()
    {

    }

    /**
     * get the difference between 2 dates
     *
     * @param string $from
     *            start date
     * @param string $to
     *            end date
     * @param string $type
     *            the type of difference to return
     * @return string or array, if type is set then a string is returned
     *         otherwise an array is returned
     */
    public static function difference($from, $to, $type = null)
    {
        $d1   = new \DateTime($from);
        $d2   = new \DateTime($to);
        $diff = $d2->diff($d1);
        if ($type == null) {
            // return array
            return $diff;
        } else {
            return $diff->$type;
        }
    }

    /**
     * Business Days
     *
     * Get number of working days between 2 dates
     *
     * Taken from
     * http://mugurel.sumanariu.ro/php-2/php-how-to-calculate-number-of-work-days-between-2-dates/
     *
     * @param string $startDate
     *            date in the format of Y-m-d
     * @param string $endDate
     *            date in the format of Y-m-d
     * @param bool $weekendDays
     *            returns the number of weekends
     * @return integer returns the total number of days
     */
    public static function businessDays($startDate, $endDate, $weekendDays = false)
    {
        $begin = strtotime($startDate);
        $end   = strtotime($endDate);

        if ($begin > $end) {
            // startDate is in the future
            return 0;
        } else {
            $numDays  = 0;
            $weekends = 0;

            while ($begin <= $end) {
                $numDays++;
// no of days in the given interval
                $whatDay = date('N', $begin);

                if ($whatDay > 5) {
// 6 and 7 are weekend days
                    $weekends++;
                }

                $begin += 86400;
// +1 day
            }

            ;

            if ($weekendDays == true) {
                return $weekends;
            }

            $workingDays = ($numDays - $weekends);
            return $workingDays;
        }
    }

    /**
     * get an array of dates between 2 dates (not including weekends)
     *
     * @param string $startDate
     *            start date
     * @param string $endDate
     *            end date
     * @param integer $nonWork
     *            day of week(int) where weekend begins - 5 = fri -> sun, 6 =
     *            sat -> sun, 7 = sunday
     * @return array list of dates between $startDate and $endDate
     */
    public static function businessDates($startDate, $endDate, $nonWork = 6)
    {
        $begin     = new \DateTime($startDate);
        $end       = new \DateTime($endDate);
        $holiday   = [];
        $interval  = new \DateInterval('P1D');
        $dateRange = new \DatePeriod($begin, $interval, $end);
        foreach ($dateRange as $date) {
            if ($date->format("N") < $nonWork and ! in_array($date->format("Y-m-d"), $holiday)) {
                $dates[] = $date->format("Y-m-d");
            }
        }

        return $dates;
    }

    public function setDateFormat($dateFormat)
    {
        $this->_dateFormat = $dateFormat;
    }

    public function setTimeFormat($timeFormat)
    {
        $this->_timeFormat = $timeFormat;
    }

    public function convertDateToTimeStamp($dateString)
    {
        return strtotime($dateString);
    }

    public function convertTimeStampToDate($time, $dateFormat = false)
    {
        if (!$dateFormat) {
            $dateFormat = $this->_dateFormat;
        }

        return date($dateFormat, strtotime($time));
    }

    public static function now()
    {
        return date("Y-m-d H:i:s");
    }

    public static function today()
    {
        return date("Y-m-d");
    }

    public static function getSqlDate()
    {
        return date("Y-m-d");
    }

    public static function getSqlDateUTC()
    {
        return gmdate("Y-m-d");
    }

    public static function getSqlDateTime($format = "Y-m-d H:i:s")
    {
        return date($format);
    }

    public static function getSqlDateTimeUTC($format = "Y-m-d H:i:s")
    {
        return gmdate($format);
    }

    /**
     *
     * @param string $dateFormat
     * @return string
     */
    public static function getThisDate($dateFormat = 'Y-m-d')
    {
        return date($dateFormat);
    }

    /**
     *
     * @param string $dateFormat
     *            default: F d, Y - h:i:s A
     * @return string e.g.Aug 15, 1947 - 12:34:55
     *         See also getThisDate()
     */
    public static function getThisDateTime($dateFormat = 'F d, Y - h:i:s A')
    {
        return date($dateFormat, time());
    }

    public function getWeekWithDate($dateString)
    {
        $timeStamp          = $this->convertDateToTimeStamp($dateString);
        $weekDay            = date('w', $timeStamp);
        $startDateTimeStamp = ($timeStamp - ($weekDay * 24 * 60 * 60));

        $thisDateTimeStamp = $startDateTimeStamp;
        for ($i = 0; $i < 7; $i++) {
            $datesArray[]       = date($this->_dateFormat, $thisDateTimeStamp);
            $thisDateTimeStamp += (1 * 24 * 60 * 60);
        }

        return $datesArray;
    }

    public static function getMonthName($monthNumber, $returnShortName = false)
    {
        switch ($monthNumber) {
            case '1':
                $monthName = 'January';
                break;
            case '2':
                $monthName = 'February';
                break;
            case '3':
                $monthName = 'March';
                break;
            case '4':
                $monthName = 'April';
                break;
            case '5':
                $monthName = 'May';
                break;
            case '6':
                $monthName = 'June';
                break;
            case '7':
                $monthName = 'July';
                break;
            case '8':
                $monthName = 'August';
                break;
            case '9':
                $monthName = 'September';
                break;
            case '10':
                $monthName = 'October';
                break;
            case '11':
                $monthName = 'November';
                break;
            case '12':
                $monthName = 'December';
                break;
        }

        return $returnShortName ? substr($monthName, 0, 3) : $monthName;
    }

    public function getDatesInMonth($month, $year, $returnType = 'normal', $dateFormat = null)
    {
        if ($dateFormat == null) {
            $dateFormat = $this->_dateFormat;
        }

        $timeStamp = strtotime('01-' . $month . '-' . $year . ' 00:00:01');

        $startDate       = date($dateFormat, $timeStamp);
        $daysInThisMonth = date('t', $timeStamp);

        $datesArray = [];
        if ($returnType == 'weekwise') {
            $datesArray[0] = [
                'Sunday',
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
            ];
            $weekCount     = 1;
            for ($i = 1; $i <= $daysInThisMonth; $i++) {
                $weekDay = date('w', $timeStamp);
                if ($weekDay == 0) {
                    $weekCount += 1;
                }

                $datesArray[$weekCount][$weekDay] = date($dateFormat, $timeStamp);
                $timeStamp += (1 * 24 * 60 * 60);
            }
        } else if ($returnType == 'keys') {
            for ($i = 1; $i <= $daysInThisMonth; $i++) {
                $datesArray[date($dateFormat, $timeStamp)] = 0;
                $timeStamp += (1 * 24 * 60 * 60);
            }
        } else {
            for ($i = 1; $i <= $daysInThisMonth; $i++) {
                $datesArray[] = date($dateFormat, $timeStamp);
                $timeStamp   += (1 * 24 * 60 * 60);
            }
        }

        return $datesArray;
    }

    public function getFirstDateInMonth($month = '', $year = '', $dateFormat = null)
    {
        if (empty($month)) {
            $month = date('m');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        if (empty($dateFormat)) {
            $dateFormat = $this->_dateFormat;
        }

        $result = strtotime("{$year}-{$month}-01");
        return date($dateFormat, $result);
    }

    public function getLastDateInMonth($month = '', $year = '', $dateFormat = null)
    {
        if (empty($month)) {
            $month = date('m');
        }

        if (empty($year)) {
            $year = date('Y');
        }

        if (empty($dateFormat)) {
            $dateFormat = $this->_dateFormat;
        }

        if ($year < 10) {
            $year = '0' . $year;
        }

        $result = strtotime("{$year}-{$month}-01");
        $result = strtotime('-1 second', strtotime('+1 month', $result));
        return date($dateFormat, $result);
    }

    public function searchFirstDate($dateString, $dateFormat = 'd-M-Y')
    {
        $timestamp = strtotime($dateString);
        $month     = date('m', $timestamp);
        $year      = date('Y', $timestamp);
        $result    = strtotime("{$year}-{$month}-01");
        return date($dateFormat, $result);
    }

    public function searchLastDate($dateString, $dateFormat = 'd-M-Y')
    {
        $timestamp = strtotime($dateString);
        $month     = date('m', $timestamp);
        $year      = date('Y', $timestamp);
        $result    = strtotime("{$year}-{$month}-01");
        $result    = strtotime('-1 second', strtotime('+1 month', $result));
        return date($dateFormat, $result);
    }

    public static function isValidDate($dateString)
    {
        $timestamp = strtotime($dateString);
        $month     = date('m', $timestamp);
        $day       = date('d', $timestamp);
        $year      = date('Y', $timestamp);
        return checkdate($month, $day, $year);
    }

    public static function formatDate($date, $format = 'd-M-Y', $returnHiphen = false)
    {
        if ($date == '') {
            return $returnHiphen ? '- ' : null;
        }

        return date($format, strtotime($date));
    }

    public static function formatDateTimeFromTimestamp($timestamp, $format = 'd-M-Y H:i:s')
    {
        if ($timestamp == '') {
            return '';
        }

        return date($format, $timestamp);
    }

    public static function formatSQLDateFromTimestamp($timestamp, $format = 'Y-m-d')
    {
        if ($timestamp == '') {
            return '';
        }

        return date($format, $timestamp);
    }

    public static function formatDateTime($date, $format = 'd-M-Y H:i:s')
    {
        if ($date == null) {
            return '';
        }

        return date($format, strtotime($date));
    }

    public static function formatSqlDate($date, $format = 'Y-m-d')
    {
        if ($date == '') {
            return null;
        }

        return date($format, strtotime($date));
    }

    public static function formatSqlDateTime($date, $format = 'Y-m-d H:i:s')
    {
        if ($date == '') {
            return null;
        }

        return date($format, strtotime($date));
    }

    public static function getTimezonesList()
    {
        $timezoneIdentifiers = \DateTimeZone::listIdentifiers();
        $utcTime = new \DateTime('now', new \DateTimeZone('UTC'));

        $tempTimezones = [];
        foreach ($timezoneIdentifiers as $timezoneIdentifier) {
            $currentTimezone = new \DateTimeZone($timezoneIdentifier);

            $tempTimezones[] = [
                'offset'     => (int) $currentTimezone->getOffset($utcTime),
                'identifier' => $timezoneIdentifier,
            ];
        }

        // Sort the array by offset,identifier ascending
        usort(
            $tempTimezones,
            function ($a, $b) {
                return ($a['offset'] == $b['offset']) ? strcmp($a['identifier'], $b['identifier']) : ($a['offset'] - $b['offset']);
            }
        );

        $timezoneList = [];
        foreach ($tempTimezones as $tz) {
            $sign   = ($tz['offset'] > 0) ? '+' : '-';
            $offset = gmdate('H:i', abs($tz['offset']));
            list ($country, $city, $place)   = explode('/', $tz['identifier']);
            $timezoneList[$tz['identifier']] = ($place != '' ? str_replace('_', ' ', $place) . ' - ' : '') . str_replace('_', ' ', $city) . ' - ' . $country . ' (GMT ' . $sign . $offset . ')';
        }

        return $timezoneList;
    }

    /*
     * Credit: https://netgen.in/convert-time-from-one-timezone-to-another/
     */

    public static function convertTimezone($time, $fromTimezone, $toTimezone, $timeFormat = 'Y-m-d H:i:s')
    {
        if ($time == '' || $fromTimezone == null || $toTimezone == null) {
            return null;
        }

        // timezone by php friendly values
        $date = new \DateTime($time, new \DateTimeZone($fromTimezone));
        $date->setTimezone(new \DateTimeZone($toTimezone));
        $time = $date->format($timeFormat);
        return $time;
    }

    /*
     * Calendar view in html
     * Modified from http://davidwalsh.name/php-calendar
     */

    public function renderHTMLCalendar($month, $year, $calendarDayEvents = null)
    {

        /* draw table */
        $calendar = '<table width="100%" border="1" cellpadding="0" cellspacing="0" class="helpers-calendar-table">';

        /* table headings */
        $headings  = [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat',
        ];
        $calendar .= '<tr class="helpers-calendar-row"><th class="helpers-calendar-day-header" width="14%">' . implode('</th><th class="helpers-calendar-day-header" width="14%">', $headings) .
            '</th></tr>';

        /* days and weeks vars now ... */
        $mktime         = mktime(0, 0, 0, $month, 1, $year);
        $runningDay     = date('w', $mktime);
        $daysInMonth    = date('t', $mktime);
        $shortMonth     = date('m', $mktime);
        $daysInThisWeek = 1;
        $dayCounter     = 0;
        $datesArray     = [];

        /* row for week one */
        $calendar .= '<tr class="helpers-calendar-row">';

        /* print "blank" days until the first of the current week */
        for ($x = 0; $x < $runningDay; $x++) {
            $calendar .= '<td class=""> </td>';
            $daysInThisWeek++;
        }

        /* keep going with days.... */
        for ($listDay = 1; $listDay <= $daysInMonth; $listDay++) {
            $calendar .= '<td class="helpers-calendar-day">';
            /* add in the day number */
            $calendar .= '<div class="helpers-calendar-date"><a href="#">' . $listDay . '</a></div>';
            $calendar .= '<div class="helpers-calendar-date-content">';
            $eventDate = $year . '-' . $shortMonth . '-' . str_pad($listDay, 2, '0', STR_PAD_LEFT);
// pad
            // zeroes
            if ($calendarDayEvents[$eventDate] != '') {
                $calendar .= '<div style="background-color:' . ($calendarDayEvents[$eventDate]['backgroundColor']) . '">';
                foreach ($calendarDayEvents[$eventDate] as $eventEntry) {
                    $calendar .= '' . $eventEntry['entry'] . '';
                }

                $calendar .= '</div>';
            }

            $calendar .= '</div>';

            $calendar .= str_repeat('<p> </p>', 2);

            $calendar .= '</td>';
            if ($runningDay == 6) {
                $calendar .= '</tr>';
                if (($dayCounter + 1) != $daysInMonth) {
                    $calendar .= '<tr class="helpers-calendar-row">';
                }

                $runningDay     = -1;
                $daysInThisWeek = 0;
            }

            $daysInThisWeek++;
            $runningDay++;
            $dayCounter++;
        }

        /* finish the rest of the days in the week */
        if ($daysInThisWeek < 8) {
            for ($x = 1; $x <= (8 - $daysInThisWeek); $x++) {
                $calendar .= '<td class="calendar-day-np"> </td>';
            }
        }

        /* final row */
        $calendar .= '</tr>';

        /* end the table */
        $calendar .= '</table>';

        /* all done, return result */
        return $calendar;
    }

    public static function secondsToTime($inputSeconds, $returnAsString = true)
    {
        $secondsInAMinute = 60;
        $secondsInAnHour  = (60 * $secondsInAMinute);
        $secondsInADay    = (60 * $secondsInAnHour);

        // extract days
        $days = floor($inputSeconds / $secondsInADay);

        // extract hours
        $hourSeconds = ($inputSeconds % $secondsInADay);
        $hours       = floor($hourSeconds / $secondsInAnHour);

        // extract minutes
        $minuteSeconds = ($hourSeconds % $secondsInAnHour);
        $minutes       = floor($minuteSeconds / $secondsInAMinute);

        // extract the remaining seconds
        $remainingSeconds = ($minuteSeconds % $secondsInAMinute);
        $seconds          = ceil($remainingSeconds);

        // return the final array
        $obj = [
            'd' => (int) $days,
            'h' => (int) $hours,
            'm' => (int) $minutes,
            's' => (int) $seconds,
        ];

        if ($returnAsString) {
            return ($days > 0 ? $days . 'd' : '') . ' ' . $hours . 'h ' . $minutes . 'm';
        } else {
            return $obj;
        }
    }

    public static function nicetime($date)
    {
        if (empty($date)) {
            return "No date provided";
        }

        $periods = [
            "second",
            "minute",
            "hour",
            "day",
            "week",
            "month",
            "year",
            "decade",
        ];
        $lengths = [
            "60",
            "60",
            "24",
            "7",
            "4.35",
            "12",
            "10",
        ];

        $now      = time();
        $unixDate = strtotime($date);

        // check validity of date
        if (empty($unixDate)) {
            return "Bad date";
        }

        // is it future date or past date
        if ($now > $unixDate) {
            $difference = ($now - $unixDate);
            $tense      = "ago";
        } else {
            $difference = ($unixDate - $now);
            $tense      = "from now";
        }

        for ($j = 0; $difference >= $lengths[$j] && $j < (count($lengths) - 1); $j++) {
            $difference /= $lengths[$j];
        }

        $difference = round($difference);

        if ($difference != 1) {
            $periods[$j] .= "s";
        }

        return "$difference $periods[$j] {$tense}";
    }

}

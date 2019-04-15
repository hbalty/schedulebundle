<?php
/**
 * @author: h.balti
 * Date: 17/01/2019
 * Time: 15:15
 * Licence: MIT
 */

namespace Geekshub\ScheduleBundle\Entity;



class Schedule
{

    private $tasks = array() ;
    private $groups = array() ;


    public function __construct()
    {

    }


    /**
     * @return $this
     */
    public function compile(){

        if (empty($this->tasks)){
            trigger_error("The schedule needs at least one task to function",E_USER_ERROR);
        }

        foreach ($this->get_groups($this->tasks) as $key => $group_name){
            $this->groups[] = (new Group($group_name,$this->get_tasks_by_group($group_name)))->make_lines() ;
        }
        return $this;
    }


    /**
     * @param $tasks
     * @return array
     */
    public function get_groups($tasks){
        // extracting all groups
        $groups = [] ;
        foreach ($tasks as $task){
            if (!in_array($task->group, $groups)){
                $groups[] = $task->group;
            }
        }

        return $groups;
    }


    /**
     * @param $group_title
     * @return array
     */
    private function get_tasks_by_group($group_title)
    {
        $result = [] ;
        foreach ($this->tasks as $task){
            if ($task->group == $group_title){
                $result[] = $task ;
            }
        }

        return $result ;
    }


    /**
     * @return array
     */
    public function getGroupedTasks(){
        return $this->groups;
    }

    /**
     * @param $label
     * @param $group
     * @param $start_date
     * @param $end_date
     * @param $real_end_date
     * @param $title
     * @param string $link
     */
    public function addTaskByAttr($label, $group,$start_date,$end_date,$real_end_date, $title, $link = ''){
        $task = new Task($start_date,$end_date,$real_end_date,$title,$group,$label,$link, []) ;
        $this->tasks[] = $task ;
    }


    /**
     * @param Task $task
     */
    public function addTask(Task $task){
        $this->tasks[] = $task ;
    }

    /**
     * @param $date
     * @return bool|int
     */
    public function convertDate($date){
        if ($date){
            $dateFormat = new \DateTime($date);
            return $dateFormat->getTimestamp();
        } else {
            return false;
        }

    }

    /**
     * @return int
     */
    public function getStartDateTimeStamp(){
        $smallestDate = $this->convertDate($this->tasks[0]->start_date);
        foreach ($this->tasks as $task){
            if ($this->convertDate($task->start_date) < $smallestDate){
                $smallestDate = $this->convertDate($task->start_date);
            }
        }
        $firstOfTheMonth = (new \DateTime())->setTimestamp($smallestDate);
        $firstOfTheMonth->modify('01-'.$firstOfTheMonth->format('m').'-'.$firstOfTheMonth->format('Y'));
        return $firstOfTheMonth->getTimestamp();
    }


    /**
     * @return int
     */
    public function getEndDateTimeStamp(){
        $biggestTimeStamp = $this->convertDate($this->tasks[0]->expected_end_date);
        if ($biggestTimeStamp){
            foreach ($this->tasks as $task){
                if ($this->convertDate($task->expected_end_date) > $biggestTimeStamp){
                    $biggestTimeStamp = $this->convertDate($task->expected_end_date);
                }
            }
        }
        $endOfTheMonth = (new \DateTime())->setTimestamp($biggestTimeStamp);
        $endOfTheMonth->modify(cal_days_in_month(CAL_GREGORIAN,$endOfTheMonth->format('m'),$endOfTheMonth->format('Y')).'-'.$endOfTheMonth->format('m').'-'.$endOfTheMonth->format('Y'));
        return $endOfTheMonth->getTimestamp();
    }


    /**
     * @param $small_period
     * @param $day
     * @return bool
     */
    public function period_intersect($small_period, $day){
        foreach ($small_period as $slot){
            if ($day->format('d/m/Y') === $slot->format('d/m/Y')){
                return true ;
            }
        }
        return false;
    }


    /**
     * @param $start_date_ts
     * @param $end_date_ts
     * @return \DatePeriod
     */
    public function cal_ts_date_period($start_date_ts, $end_date_ts){
        $start_date = (new \DateTime())->setTimestamp($start_date_ts);
        $end_date = (new \DateTime())->setTimestamp($end_date_ts);
        $end_date->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 day');
        return new \DatePeriod($start_date, $interval ,$end_date);
    }


    /**
     * @param $start_date
     * @param $end_date
     * @return \DatePeriod
     */
    public function cal_date_period($start_date, $end_date){
        $start_date = new \DateTime($start_date);
        $end_date = new \DateTime($end_date);
        $end_date->modify('+1 day');
        $interval = \DateInterval::createFromDateString('1 day');
        return  new \DatePeriod($start_date, $interval ,$end_date);
    }


    /**
     * @param $start_date
     * @param $end_date
     * @param $year
     * @param bool $show_weekends
     * @return int
     */
    public function extractRemainingDays($start_date,$end_date, $year, $show_weekends = false){
        $lastMonthOfYear = new \DateTime('31-12-'.$year);
        $interval = \DateInterval::createFromDateString('1 day');
        $days = 0 ;
        if ($lastMonthOfYear <= $end_date){
            $period = new \DatePeriod($start_date,$interval, $lastMonthOfYear);
            foreach ($period as $day){
                if ($show_weekends){
                    $days ++ ;
                } else{
                    if (!in_array($day->format('w'), [0,6])){
                        $days ++ ;
                    }
                }

            }
        } else {
            $period = new \DatePeriod($start_date,$interval, $end_date);
            foreach ($period as $day){
                if ($show_weekends){
                    $days ++ ;
                }else{
                    if (!in_array($day->format('w'), [0,6])) {
                        $days++;
                    }
                }

            }
        }

        return $days + 1 ;

    }

    /**
     * @param $starDate
     * @param $endDate
     * @param bool $show_weekends
     * @return array
     */
    public function getYears($starDate, $endDate, $show_weekends = false){
        $datetimeFormat = 'Y-m-d';
        $date1 = new \DateTime();
        $date1->setTimestamp($starDate);
        $date1->format($datetimeFormat);

        $date2 = new \DateTime();
        $date2->setTimestamp($endDate);
        $date2->format($datetimeFormat);


        $yearsArray = [] ;


        for ($year = $date1->format('Y'); $year <= $date2->format('Y'); $year++){
            if ($year <= $date2->format('Y')){
                if (count($yearsArray) > 0 ){
                    $firstOfTheMonth = new \DateTime('01-01-'.$year);
                    $yearsArray[] = array($year, $this->extractRemainingDays($firstOfTheMonth,$date2,$year,$show_weekends),$firstOfTheMonth,$date2);
                } else {
                    $yearsArray[] = array($year, $this->extractRemainingDays($date1,$date2,$year,$show_weekends), $date1, $date2);
                }

            }
        }
        $fullInterval = $date1->diff($date2);
        return  $yearsArray;
    }


    /**
     * @param $starDate
     * @param $endDate
     * @param bool $show_weekends
     * @return array
     */
    public function getWeeks($starDate, $endDate,$show_weekends = false){
        $datetimeFormat = 'Y-m-d';
        $date1 = new \DateTime();
        $date1->setTimestamp($starDate);
        $date1->modify('01-'.$date1->format('m').'-'.$date1->format('Y'));
        $date1->format($datetimeFormat);

        $date2 = new \DateTime();
        $date2->setTimestamp($endDate);
        $date2->modify(cal_days_in_month(CAL_GREGORIAN,$date2->format('m'),$date2->format('Y')).'-'.$date2->format('m').'-'.$date2->format('Y'));
        $date2->format($datetimeFormat);
        $weeksArray = [] ;

        if ($show_weekends){
            if (!in_array(intval($date1->format('w')),[1])){
                $remainingDaysUntilMonday = 6 - intval($date1->format('w'));
                $weeksArray[] = array(intval($date1->format('W')) ,$remainingDaysUntilMonday);
                $date1->modify($remainingDaysUntilMonday.' day');

            }
        }else{
            if (!in_array(intval($date1->format('w')),[0,1,6])){
                $remainingDaysUntilMonday = 6 - intval($date1->format('w'));
                $weeksArray[] = array(intval($date1->format('W')) ,$remainingDaysUntilMonday);
                $date1->modify($remainingDaysUntilMonday.' day');

            }
        }


        while($date1 <= $date2){
            if ($show_weekends){
                $weeksArray[] = array(intval($date1->format('W')) + 1, 7);
            }else{
                $weeksArray[] = array(intval($date1->format('W')) + 1, 5);
            }

            $date1->modify('1 week');
        }

        return  $weeksArray;
    }


    /**
     * @param $starDate
     * @param $endDate
     * @param bool $show_weekends
     * @return array
     */
    public function getMonths($starDate, $endDate, $show_weekends  = false){
        $datetimeFormat = 'Y-m-d';
        $date1 = new \DateTime();
        $date1->setTimestamp($starDate);
        $date1->modify('01-'.$date1->format('m').'-'.$date1->format('Y'));
        $date1->format($datetimeFormat);

        $date2 = new \DateTime();
        $date2->setTimestamp($endDate);
        $date2->modify(cal_days_in_month(CAL_GREGORIAN,$date2->format('m'),$date2->format('Y')).'-'.$date2->format('m').'-'.$date2->format('Y'));
        $date2->format($datetimeFormat);

        $monthsArray = [] ;
        $daysToIgnore = [] ;

        if (!$show_weekends){
            $daysToIgnore = [0,6];
        }

        while($date1 <= $date2){
            $monthsArray[] = array($date1->format('F'), $this->cal_week_days($date1->format('m'),$date1->format('Y'),$daysToIgnore));
            $date1->modify('1 month');
        }

        return  $monthsArray;
    }


    /**
     * @param $month
     * @param $year
     * @param $ignore
     * @return int
     */
    public function cal_week_days($month,$year, $ignore){
        $count = 0;
        $counter = mktime(0, 0, 0, $month, 1, $year);
        while (date("n", $counter) == $month) {
            if (in_array(date("w", $counter), $ignore) == false) {
                $count++;
            }
            $counter = strtotime("+1 day", $counter);
        }
        return $count;


    }


    /**
     * @param $dateTimestamp
     * @return bool
     * verifies whether a time stamp is weekend
     */
    public function isWeekend($dateTimestamp){
        $date = new \DateTime();
        $date->setTimestamp($dateTimestamp);
        if (in_array($date->format('w'), [0,6])){
            return true;
        } else {
            return false ;
        }
    }


    /**
     * @param $starDate
     * @param $endDate
     * @return array
     */
    public function getDays($starDate, $endDate){
        $datetimeFormat = 'Y-m-d';
        $date1 = new \DateTime();
        $date1->setTimestamp($starDate);
        $date1->format($datetimeFormat);

        $date2 = new \DateTime();
        $date2->setTimestamp($endDate);
        $date2->format($datetimeFormat);

        $daysArray= [] ;


        while($date1 <= $date2){
            $daysArray[$date1->getTimestamp()] = $date1->format('d');
            $date1->modify('+1 day');
        }


        return  $daysArray;
    }







}
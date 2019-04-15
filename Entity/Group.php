<?php
/**
 * @author: h.balti
 * Date: 09/03/2019
 * Time: 20:53
 * Licence: MIT
 */

namespace Geekshub\ScheduleBundle\Entity;


class Group
{
    public $title ;
    public $lines ;
    private $tasks ; // to be removed


    /**
     * Group constructor.
     * @param $title
     * @param $tasks
     */
    public function __construct($title,$tasks)
    {
        $this->title = $title ;
        $this->lines = [] ;
        $this->tasks = $tasks ;

        return $this;
    }


    /**
     * @return $this
     */
    public function make_lines(){
        $tasks = $this->tasks;
        $lines = [] ;
        $providers = self::get_labels($tasks);
        foreach ($providers as $provider){
            usort($provider, array('Geekshub\ScheduleBundle\Entity\Group','cmpTasks')) ;
            $line_to_be_inserted = [] ;
            while($x = array_pop($provider)) {
                // verify if task doesnt overlap with line
                if (!empty($lines) && self::check_empty_spot($x, $lines) !== -1) {
                    $lines[self::check_empty_spot($x, $lines)][] = $x;
                } else if (!self::isTaskOverlapping($x, $line_to_be_inserted)) {
                    $line_to_be_inserted[] = $x;
                } else {
                    if (count($line_to_be_inserted) > 0){
                        $lines[] = $line_to_be_inserted;
                    }
                    $line_to_be_inserted = [];
                    $line_to_be_inserted[] = $x;

                }
            }
            if (!empty($line_to_be_inserted)){
                $lines[] = $line_to_be_inserted ;
            }



        }
        $this->lines = $lines;
        return $this ;


    }


    public static function check_empty_spot($task, $lines){
        foreach ($lines as $index =>  $line){
            if (count($line) > 0){
                if ($line[0]->label === $task->label){
                    if (!self::isTaskOverlapping($task, $line)){
                        return $index;
                    }
                }

            }

        }
        return -1 ;
    }


    /**
     * @param $task
     * @param $line_array
     * @return bool
     */
    public static function isTaskOverlapping($task, $line_array){
        foreach ($line_array as $test_task){
            if (self::dateOverlapCheck($task,$test_task)){
                return true ;
            }
        }

        return false ; // date doesn't exist
    }

    /**
     * @param $task1
     * @param $task2
     * @return bool
     */
    public static function dateOverlapCheck($task1, $task2){
        $sDateA = new \DateTime($task1->start_date); // first task start date
        if ($task1->real_end_date != null){
            $eDateA = new \DateTime($task1->real_end_date);
        } else {
            $eDateA = new \DateTime($task1->expected_end_date); // first task end date
        }



        $sDateB = new \DateTime($task2->start_date); // second task start date

        if ($task1->real_end_date != null){
            $eDateB = new \DateTime($task2->real_end_date);
        } else {
            $eDateB = new \DateTime($task2->expected_end_date); // second task end date
        }



        return (( $sDateA <= $eDateB) and ($eDateA >= $sDateB)) ;
    }


    /**
     * @param $tasks
     * @return array
     */
    public static function get_labels($tasks){
        $result = [] ;
        foreach ($tasks as $task){
            $result[$task->label][] = $task ;
        }

        return $result;
    }

    /**
     * @param $task1
     * @param $task2
     * @return bool
     */
    private static function cmpTasks($task1, $task2){ // sort tasks
        $sDateA = new \DateTime($task1->start_date); // first task start date

        $sDateB = new \DateTime($task2->start_date); // second task start date
        

        return ($sDateA < $sDateB) ;
    }


}
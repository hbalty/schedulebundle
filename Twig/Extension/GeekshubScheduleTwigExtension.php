<?php
/**
 * @author: h.balti
 * Date: 18/01/2019
 * Time: 11:53
 * Licence: MIT
 */

namespace  Geekshub\ScheduleBundle\Twig\Extension;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Geekshub\ScheduleBundle\Entity\Task;


class GeekshubScheduleTwigExtension extends \Twig_Extension
{
    protected $container ;
    protected $schedule;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->schedule = $container->get('geekshub_schedule') ;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(){
        return array(
            new \Twig_SimpleFunction("gh_render_planning", array($this, "renderPlanning")),
            new \Twig_SimpleFunction("gh_schedule", array($this, "getSchedule")),
            new \Twig_SimpleFunction("gh_verify_date", array($this, "isDateInSchedule")),
            new \Twig_SimpleFunction("gh_isFirstDay", array($this, "isFirstDay")),
            new \Twig_SimpleFunction("gh_isDelay", array($this, "isDelay")),
            new \Twig_SimpleFunction("gh_wrap", array($this, "wrap_text"))
        ) ;
    }


    /**
     * @param $text
     * @param Task $task
     * @return bool|string
     */
    public function wrap_text($text,Task $task){
        $startDate = new \DateTime($task->start_date) ;
        $endDate = new \DateTime($task->expected_end_date) ;

        $numberOfDays= date_diff($startDate,$endDate) ;
        return substr($text,0, $numberOfDays->d * 2) ;


    }


    /**
     * @param array $options
     * @return mixed
     */
    public function renderPlanning(array $options = array()){
        return $this->container->get('geekshub_schedule.helper')->schedule($options) ;
    }


    /**
     * @param $timestamp
     * @param $line_array
     * @return mixed
     */
    public function isDateInSchedule($timestamp, $line_array){

        for ($i = 0; $i < count($line_array) ; $i++){
            $start = (new \DateTime($line_array[$i]->start_date))->getTimestamp() ;
            if ($line_array[$i]->real_end_date != null){
                $end = (new \DateTime($line_array[$i]->real_end_date))->getTimestamp() ;
            }else{
                $end = (new \DateTime($line_array[$i]->expected_end_date))->getTimestamp() ;
            }
            if ($timestamp <= $end && $timestamp >= $start){
                return $line_array[$i];
            }
        }

        return false ; // date doesn't exist
    }


    /**
     * @param $timestamp
     * @param $task
     * @return bool
     */
    public function isFirstDay($timestamp, $task){
        $startDateTS = (new \DateTime($task->start_date))->getTimestamp();
        if ($timestamp == $startDateTS){
            return true ;
        } else{
            return false ;
        }

    }

    /**
     * @param $timestamp
     * @param Task $task
     * @return bool
     */
    public function isDelay($timestamp,Task $task){
        if ($task->real_end_date == null){
            return false ;
        }

        $expected_end_date = (new \DateTime($task->expected_end_date))->getTimestamp() ; // end date
        $real_end_date = (new \DateTime($task->real_end_date))->getTimestamp() ; // real end date

        if ($timestamp <= $real_end_date && $timestamp > $expected_end_date){
            return true ;
        } else{
            return false;
        }
    }


    /**
     * @return object
     */
    public function getSchedule(){
        return $this->schedule ;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "schedule";
    }



}


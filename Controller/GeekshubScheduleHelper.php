<?php
/**
 * @author: h.balti
 * Date: 18/01/2019
 * Time: 11:04
 * Licence: MIT
 */

namespace Geekshub\ScheduleBundle\Controller;

use Geekshub\ScheduleBundle\Entity\Schedule;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Templating\Helper\Helper;

class GeekshubScheduleHelper extends Helper
{


    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * @var $schedule
     */
    protected $schedule;

    /**
     * @var array The default options load from config file
     */
    protected $options = array();

    public function __construct(EngineInterface $templating, Schedule $schedule, array $options)
    {
        $this->templating = $templating;
        $this->schedule = $schedule;
        $this->options = array_merge($options, array(
            'scheduleTemplate' => $options['scheduleTemplate'],
        ));


    }





    /**
     * Returns the HTML of the schedule
     *
     * @param array $options The user-supplied options from the view
     * @return string A HTML string
     */
    public function schedule(array $options = array())
    {

        $options = $this->resolveOptions($options);

        return $this->templating->render(
            $options['scheduleTemplate'],
            $options
        );


    }


    /**
     * @return string
     */
    public function getName()
    {
        return 'schedule';
    }

    /**
     * Merges user-supplied options from the view
     * with base config values
     *
     * @param array $options The user-supplied options from the view
     * @return array
     */
    private function resolveOptions(array $options = array())
    {
        return array_merge($this->options, $options);
    }



}
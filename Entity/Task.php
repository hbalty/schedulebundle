<?php
/**
 * @author: h.balti
 * Date: 17/01/2019
 * Time: 15:45
 * Licence: MIT
 */

namespace Geekshub\ScheduleBundle\Entity;


class Task
{
    const REAL_RECEPTION_DATE = 0 ;

    public $start_date ;
    public $expected_end_date ;
    public $real_end_date ;
    public $title;
    public $group;
    public $label;
    public $link;





    public function __construct($start_date, $expected_end_date, $real_end_date ,$title, $group, $label,$link = '', $options = self::REAL_RECEPTION_DATE)
    {
        $this->start_date = $start_date;
        $this->expected_end_date = $expected_end_date;
        $this->real_end_date = $real_end_date;
        $this->title = $title;
        $this->group = $group;
        $this->label = $label;
        $this->link = $link ;
    }

}
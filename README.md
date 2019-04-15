
DEMO
============
You can find a demonstration of what the bundle does on : [geekshub.net](http://www.geeks-hub.net/)


Installation
============

###### 1. Configure templating for your application

``` yaml
# app/config/config.yml (Symfony <=3)
framework:
    templating:
        engines: ['twig']
```


###### 2. Install this bundle using [Composer](https://getcomposer.org/)

``` bash
composer require geekshub/schedulebundle
```
###### 3. Add the schedule bundle to your application's kernel

``` php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new \Geekshub\ScheduleBundle\GeekshubScheduleBundle(),
        // ...
    );
}
```
###### 4. Configure the bundle
``` yaml
# app/config/config.yml
geekshub_schedule: ~
```
This is a basic configuration, the bundle will use the default values for the configs entries that you can find at the [Configuration](#configuration) section.

Usage
=====

In your controller:

To add tasks to your schedule you can use the `addTaskByAttr()` method 
`addTaskByAttr($label, $group, $start_date, $expected_end_date, $real_end_date, $title, $link)`

The `addTaskByAttr()` method takes seven parameters : 

Parameter | Description | Type | required |  
--------- | ----------- | ---- | -------- |
`$label` | Rendered in the first column | text | yes |
`$group` | Creates the separtation between type of entries |text | yes |
`$start_date` | Task start date| formatted date (d-m-Y) | yes |
`$expected_end_date` | Task expected end date | formatted date (d-m-Y) | yes |
`$real_end_date` | Task real end date (to calculate delay) | formatted date (d-m-Y) | no |
`$title` | Will be rendered on the task timeline | text | no |
`$link` | Will redirect you once the title is clicked | bool | no |

For example: 
``` php
public function showPlaningAction()
{
   // retrieve the schedule service from container
   $schedule = $this->container->get('geekshub_schedule'); 
   
   // retrieve the data you want to add to the schedule
   $tasks = $this->getManager()->getRepository('Yourclass')->getScheduledTasks(); 
   
   // Loop over the tasks and add them to the schedule 
   
   foreach($tasks as $task){
      $schedule->addTaskByAttr(...) ; 
   }
}
```

> **NOTE:** 
For Symfony 4.* you will have to use the dependency inejection to retrieve the schedule. 
It should look like this : 

``` php

public function showPlaningAction(Schedule $schedule) // dependency injection
{
   // ... //
}
```



 



Configuration
=============
The following default parameters can be overriden in your config.yml or similar:

``` yaml
geekshub_schedule:
    show_weekends: false
    use_link: true
    separator_color: "#bdbdbd"
    style:
      taskCellClass: "task_cell"
      yearRowClass: "years_row"
      weekRowClass: "weeks_row"
      monthRowClass: "months_row"
      dayRowClass: "days_row"
      groupRowClass: "group_row"
      taskRowClass: "task_row"
      taskCellDelayClass: "task_cell_delay"
```


Overriding the template
=======================
You can override the template by copying the `Resources/views/table.html.twig` file in your own bundle and then pass it as a parameter to the `gh_render_planning`. 
Use the `scheduleTemplate` configuration parameter:
```
{{ gh_render_planning({ scheduleTemplate: "YourOwnBundle::YourSchedule.html.twig" }) }}
```
Contributing
=============
We welcome contributions to this project, including pull requests and issues (and discussions on existing issues). We accept any suggestion to improve the bundle to server a bigger purpose.

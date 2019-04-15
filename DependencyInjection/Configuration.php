<?php
/**
 * @author: h.balti
 * Date: 13/03/2019
 * Time: 18:26
 * Licence: MIT
 */

namespace Geekshub\ScheduleBundle\DependencyInjection;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('geekshub_schedule');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = $treeBuilder->root("geekshub_schedule");
        }

        $rootNode->
            children()
                ->scalarNode("scheduleTemplate")->defaultValue("GeekshubScheduleBundle::table.html.twig")->end()
                ->scalarNode("use_links")->defaultFalse()->end()
                ->scalarNode("show_weekends")->defaultFalse()->end()
                ->scalarNode("separator_color")->defaultValue("#bdbdbd")->end()
                ->arrayNode("style")
                    ->children()
                        ->scalarNode("tableClass")->defaultValue("geekshub_schedule")->end()
                        ->scalarNode("yearRowClass")->defaultValue("years_row")->end()
                        ->scalarNode("monthRowClass")->defaultValue("months_row")->end()
                        ->scalarNode("weekRowClass")->defaultValue("weeks_row")->end()
                        ->scalarNode("dayRowClass")->defaultValue("days_row")->end()
                        ->scalarNode("groupRowClass")->defaultValue("group_row")->end()
                        ->scalarNode("taskCellClass")->defaultValue("task_cell")->end()
                        ->scalarNode("taskRowClass")->defaultValue("task_row")->end()
                        ->scalarNode("taskCellDelayClass")->defaultValue("task_cell_delay")->end()
                    ->end()
                ->end()
                ->end() ;


        return $treeBuilder ;

    }

}
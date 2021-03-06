<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\ConfigurationNode;

use Fuz\Framework\Api\ConfigurationNodeInterface;
use Monolog\Logger;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class LoggerConfigurationNode implements ConfigurationNodeInterface
{
    public function getConfigurationNode()
    {
        $levels = array_keys(Logger::getLevels());
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('logger');
        $rootNode
           ->children()
                ->enumNode('level')
                    ->values($levels)
                    ->defaultValue(reset($levels))
                ->end()
                ->integerNode('max_files')
                    ->defaultValue(30)
                ->end()
                ->scalarNode('name')
                    ->defaultValue('app.log')
                    ->validate()
                        ->ifTrue(function ($file) {
                            return strpos($file, '/') !== false;
                        })
                        ->thenInvalid("The log file name can't contain a slash ( / ): %s")
                        ->ifTrue(function ($file) {
                            return is_file($file) && !is_writeable($file);
                        })
                        ->thenInvalid('The log file exists but is not writeable: %s')
                    ->end()
                ->end()
           ->end()
        ;

        return $rootNode;
    }
}

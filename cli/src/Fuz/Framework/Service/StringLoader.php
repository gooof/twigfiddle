<?php

/*
 * This file is part of twigfiddle.com project.
 *
 * (c) Alain Tiemblo <alain@fuz.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Fuz\Framework\Service;

use Fuz\Framework\StringLoader\StringLoaderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class StringLoader
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function load($content, $format)
    {
        $service = null;
        $serviceIds = $this->container->findTaggedServiceIds('string.loader');
        foreach ($serviceIds as $serviceId => $tags) {
            foreach ($tags as $tag) {
                if (!array_key_exists('supports', $tag)) {
                    continue;
                }
                if (in_array(strtolower($format), array_map('trim', array_map('strtolower', explode('/', $tag['supports']))))) {
                    $service = $this->container->get($serviceId);
                    break;
                }
            }
            if (!is_null($service)) {
                break;
            }
        }
        if (is_null($service)) {
            throw new \InvalidArgumentException("Format {$format} was not found.");
        }
        if (!($service instanceof StringLoaderInterface)) {
            throw new \LogicException("The {$format} string loader has been found, but does not implement StringLoaderInterface.");
        }

        return $service->load($content);
    }
}

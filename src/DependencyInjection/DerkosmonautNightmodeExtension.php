<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class DerkosmonautNightmodeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__.'/../../config'));
        $loader->load('services.yaml');

        // Ist OpenTimes installiert, liefert es die Öffnungszeiten. Sonst bleibt der leere Anbieter aktiv.
        if (class_exists('Derkosmonaut\OpenTimesBundle\Resolver\OpeningHoursResolver')) {
            $loader->load('services_opentimes.yaml');
        }
    }
}

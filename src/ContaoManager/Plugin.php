<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\ContaoManager;

use Contao\CoreBundle\ContaoCoreBundle;
use Contao\ManagerPlugin\Bundle\BundlePluginInterface;
use Contao\ManagerPlugin\Bundle\Config\BundleConfig;
use Contao\ManagerPlugin\Bundle\Parser\ParserInterface;
use Derkosmonaut\NightmodeBundle\DerkosmonautNightmodeBundle;

class Plugin implements BundlePluginInterface
{
    public function getBundles(ParserInterface $parser): array
    {
        return [
            BundleConfig::create(DerkosmonautNightmodeBundle::class)
                // nach OpenTimes laden, falls installiert (sonst wird der Eintrag ignoriert)
                ->setLoadAfter([ContaoCoreBundle::class, 'Derkosmonaut\OpenTimesBundle\DerkosmonautOpenTimesBundle']),
        ];
    }
}

<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class DerkosmonautNightmodeBundle extends Bundle
{
    public function getPath(): string
    {
        // Bundle-Wurzel, damit Contao das Verzeichnis "contao/" (DCA, Sprachen, Templates) findet
        return \dirname(__DIR__);
    }
}

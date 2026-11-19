<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Schedule;

use DateTimeImmutable;

/**
 * Aktiv, solange kein Öffnungszeiten-Modul installiert ist: kennt keine Orte und keine Zeiten.
 */
final class NullScheduleProvider implements ScheduleProviderInterface
{
    public function getLocations(): array
    {
        return [];
    }

    public function getOpenIntervals(int $locationId, DateTimeImmutable $from, DateTimeImmutable $to): ?array
    {
        return null;
    }
}

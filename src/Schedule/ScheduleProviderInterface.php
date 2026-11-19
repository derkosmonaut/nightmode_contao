<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Schedule;

use DateTimeImmutable;

/**
 * Quelle der Öffnungszeiten. Umgesetzt von OpenTimesScheduleProvider; hält die Umschaltlogik frei von Datenbank und Contao.
 */
interface ScheduleProviderInterface
{
    /**
     * Auswählbare Orte für die Seiteneinstellungen.
     *
     * @return array<int, string> ID => Name
     */
    public function getLocations(): array;

    /**
     * Öffnungsintervalle zwischen $from und $to, Schließungen bereits herausgeschnitten.
     * null = für diesen Ort ist nichts bekannt (kein Plan, Ort gelöscht).
     *
     * @return list<Interval>|null
     */
    public function getOpenIntervals(int $locationId, DateTimeImmutable $from, DateTimeImmutable $to): ?array;
}

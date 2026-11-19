<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Integration\OpenTimes;

use DateTimeImmutable;
use Derkosmonaut\NightmodeBundle\Schedule\Interval;
use Derkosmonaut\NightmodeBundle\Schedule\ScheduleProviderInterface;
use Derkosmonaut\OpenTimesBundle\Repository\OpeningHoursRepository;
use Derkosmonaut\OpenTimesBundle\Resolver\OpeningHoursResolver;
use Doctrine\DBAL\Connection;

/**
 * Anbindung an derkosmonaut/opentimes-contao. Wird nur registriert, wenn das Paket installiert ist.
 * Schließungen, Ausnahmen, Feiertage und Saisonzeiten wertet der OpenTimes-Resolver aus.
 */
final class OpenTimesScheduleProvider implements ScheduleProviderInterface
{
    public function __construct(
        private readonly OpeningHoursRepository $repository,
        private readonly OpeningHoursResolver $resolver,
        private readonly Connection $connection,
    ) {
    }

    public function getLocations(): array
    {
        $rows = $this->connection->fetchAllKeyValue('SELECT id, title FROM tl_oh_location ORDER BY title');

        $locations = [];

        foreach ($rows as $id => $title) {
            $locations[(int) $id] = (string) $title;
        }

        return $locations;
    }

    public function getOpenIntervals(int $locationId, DateTimeImmutable $from, DateTimeImmutable $to): ?array
    {
        if (null === $this->repository->findLocation($locationId)) {
            return null;
        }

        $hours = $this->repository->loadHours($locationId, $from->getTimezone());

        if (!$hours->hasPlan()) {
            return null;
        }

        // ein Tag mehr, damit auch das letzte angebrochene Datum vollständig erfasst ist
        $days = (int) $from->setTime(0, 0)->diff($to->setTime(0, 0))->days + 2;

        $intervals = [];

        foreach ($this->resolver->getIntervals($hours, $from, $days) as $interval) {
            if ($interval->end > $from && $interval->start < $to) {
                $intervals[] = new Interval($interval->start, $interval->end);
            }
        }

        return $intervals;
    }
}

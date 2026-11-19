<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Screen;

use DateTimeImmutable;
use Derkosmonaut\NightmodeBundle\Schedule\Interval;
use Derkosmonaut\NightmodeBundle\Schedule\ScheduleProviderInterface;

/**
 * Entscheidet, ob ein Screen gerade normalen Inhalt oder den Alternativinhalt zeigt.
 *
 * Reihenfolge:
 *   1. Override ("immer an" / "immer aus")
 *   2. Kein Ort oder keine Zeiten bekannt → normaler Inhalt (lieber Inhalt als ein dunkler Screen)
 *   3. Öffnungszeiten, erweitert um Vor- und Nachlauf
 */
final class ScreenStateResolver
{
    /** Wie weit voraus der nächste Wechsel gesucht wird. */
    private const HORIZON_DAYS = 14;

    public function __construct(private readonly ScheduleProviderInterface $provider)
    {
    }

    public function resolve(ScreenConfig $config, DateTimeImmutable $now): ScreenState
    {
        if (ScreenConfig::OVERRIDE_ON === $config->override) {
            return new ScreenState(true, ScreenState::REASON_OVERRIDE);
        }

        if (ScreenConfig::OVERRIDE_OFF === $config->override) {
            return new ScreenState(false, ScreenState::REASON_OVERRIDE);
        }

        if ($config->locationId < 1) {
            return new ScreenState(true, ScreenState::REASON_NO_LOCATION);
        }

        $lead = max(0, $config->leadMinutes);
        $lag = max(0, $config->lagMinutes);

        // Nachlauf rückwärts mit abdecken, damit ein gerade beendetes Fenster noch zählt
        $from = $now->modify(\sprintf('-%d minutes', $lag))->modify('-1 day');
        $to = $now->modify(\sprintf('+%d days', self::HORIZON_DAYS))->modify(\sprintf('+%d minutes', $lead));

        $intervals = $this->provider->getOpenIntervals($config->locationId, $from, $to);

        if (null === $intervals) {
            return new ScreenState(true, ScreenState::REASON_UNKNOWN);
        }

        $windows = $this->expand($intervals, $lead, $lag);

        foreach ($windows as $window) {
            if ($window->contains($now)) {
                return new ScreenState(true, ScreenState::REASON_SCHEDULE, $window->end);
            }
        }

        foreach ($windows as $window) {
            if ($window->start > $now) {
                return new ScreenState(false, ScreenState::REASON_SCHEDULE, $window->start);
            }
        }

        return new ScreenState(false, ScreenState::REASON_SCHEDULE);
    }

    /**
     * Erweitert die Öffnungsintervalle um Vor- und Nachlauf und führt Überschneidungen zusammen.
     * Liegt zwischen zwei Fenstern weniger als Vor- plus Nachlauf, bleibt der Screen durchgehend an.
     *
     * @param list<Interval> $intervals
     *
     * @return list<Interval>
     */
    private function expand(array $intervals, int $lead, int $lag): array
    {
        $expanded = array_map(
            static fn (Interval $i): Interval => new Interval(
                $i->start->modify(\sprintf('-%d minutes', $lead)),
                $i->end->modify(\sprintf('+%d minutes', $lag)),
            ),
            $intervals,
        );

        usort($expanded, static fn (Interval $a, Interval $b): int => $a->start <=> $b->start);

        $merged = [];

        foreach ($expanded as $interval) {
            $last = array_key_last($merged);

            if (null !== $last && $interval->start <= $merged[$last]->end) {
                if ($interval->end > $merged[$last]->end) {
                    $merged[$last] = new Interval($merged[$last]->start, $interval->end);
                }

                continue;
            }

            $merged[] = $interval;
        }

        return $merged;
    }
}

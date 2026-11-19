<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Tests\Screen;

use DateTimeImmutable;
use DateTimeZone;
use Derkosmonaut\NightmodeBundle\Schedule\Interval;
use Derkosmonaut\NightmodeBundle\Schedule\ScheduleProviderInterface;
use Derkosmonaut\NightmodeBundle\Screen\ScreenConfig;
use Derkosmonaut\NightmodeBundle\Screen\ScreenState;
use Derkosmonaut\NightmodeBundle\Screen\ScreenStateResolver;
use PHPUnit\Framework\TestCase;

/**
 * Beispielort: täglich 09-12 und 13-17 (Mittagspause 60 Minuten), Montag 2026-01-12.
 */
final class ScreenStateResolverTest extends TestCase
{
    private DateTimeZone $tz;

    protected function setUp(): void
    {
        $this->tz = new DateTimeZone('Europe/Berlin');
    }

    public function testOpenShowsRegularContentUntilClosing(): void
    {
        $state = $this->resolve($this->config(), '2026-01-12 10:00');

        self::assertTrue($state->active);
        self::assertSame(ScreenState::REASON_SCHEDULE, $state->reason);
        self::assertSame('2026-01-12 12:00', $state->nextChange?->format('Y-m-d H:i'));
    }

    public function testClosedAtNightShowsAlternativeUntilNextOpening(): void
    {
        $state = $this->resolve($this->config(), '2026-01-12 22:00');

        self::assertFalse($state->active);
        self::assertSame('2026-01-13 09:00', $state->nextChange?->format('Y-m-d H:i'));
    }

    public function testClosingTimeIsExclusive(): void
    {
        self::assertFalse($this->resolve($this->config(), '2026-01-12 17:00')->active);
        self::assertTrue($this->resolve($this->config(), '2026-01-12 16:59')->active);
    }

    public function testLeadAndLagExtendTheWindow(): void
    {
        $config = $this->config(lead: 15, lag: 10);

        self::assertTrue($this->resolve($config, '2026-01-12 08:45')->active);
        self::assertFalse($this->resolve($config, '2026-01-12 08:44')->active);
        self::assertTrue($this->resolve($config, '2026-01-12 17:09')->active);

        $closed = $this->resolve($config, '2026-01-12 17:10');
        self::assertFalse($closed->active);
        self::assertSame('2026-01-13 08:45', $closed->nextChange?->format('Y-m-d H:i'));
    }

    public function testShortBreakIsBridgedByLeadAndLag(): void
    {
        // 30 + 30 Minuten decken die einstündige Mittagspause ab
        $state = $this->resolve($this->config(lead: 30, lag: 30), '2026-01-12 12:30');

        self::assertTrue($state->active);
        self::assertSame('2026-01-12 17:30', $state->nextChange?->format('Y-m-d H:i'));
    }

    public function testLunchBreakWithoutBridgeShowsAlternative(): void
    {
        $state = $this->resolve($this->config(lead: 10, lag: 10), '2026-01-12 12:30');

        self::assertFalse($state->active);
        self::assertSame('2026-01-12 12:50', $state->nextChange?->format('Y-m-d H:i'));
    }

    public function testOverrideWinsOverSchedule(): void
    {
        $on = $this->resolve($this->config(override: ScreenConfig::OVERRIDE_ON), '2026-01-12 22:00');
        $off = $this->resolve($this->config(override: ScreenConfig::OVERRIDE_OFF), '2026-01-12 10:00');

        self::assertTrue($on->active);
        self::assertFalse($off->active);
        self::assertSame(ScreenState::REASON_OVERRIDE, $off->reason);
        self::assertNull($off->nextChange);
    }

    public function testWithoutLocationRegularContentIsShown(): void
    {
        $state = $this->resolve($this->config(location: 0), '2026-01-12 22:00');

        self::assertTrue($state->active);
        self::assertSame(ScreenState::REASON_NO_LOCATION, $state->reason);
    }

    public function testUnknownScheduleShowsRegularContent(): void
    {
        $resolver = new ScreenStateResolver($this->provider(null));
        $state = $resolver->resolve($this->config(), $this->at('2026-01-12 22:00'));

        self::assertTrue($state->active);
        self::assertSame(ScreenState::REASON_UNKNOWN, $state->reason);
    }

    public function testNoUpcomingOpeningStaysOff(): void
    {
        // z. B. unbefristete spontane Schließung: der Anbieter liefert keine Intervalle mehr
        $resolver = new ScreenStateResolver($this->provider([]));
        $state = $resolver->resolve($this->config(), $this->at('2026-01-12 10:00'));

        self::assertFalse($state->active);
        self::assertNull($state->nextChange);
    }

    private function resolve(ScreenConfig $config, string $now): ScreenState
    {
        return (new ScreenStateResolver($this->provider($this->dailyIntervals())))->resolve($config, $this->at($now));
    }

    private function config(int $location = 1, int $lead = 0, int $lag = 0, string $override = ScreenConfig::OVERRIDE_NONE): ScreenConfig
    {
        return new ScreenConfig(10, 10, $location, $lead, $lag, override: $override, overridePageId: '' === $override ? 0 : 10);
    }

    /**
     * @return list<Interval>
     */
    private function dailyIntervals(): array
    {
        $intervals = [];

        foreach (['2026-01-11', '2026-01-12', '2026-01-13', '2026-01-14'] as $day) {
            $intervals[] = new Interval($this->at($day.' 09:00'), $this->at($day.' 12:00'));
            $intervals[] = new Interval($this->at($day.' 13:00'), $this->at($day.' 17:00'));
        }

        return $intervals;
    }

    /**
     * @param list<Interval>|null $intervals
     */
    private function provider(?array $intervals): ScheduleProviderInterface
    {
        return new class($intervals) implements ScheduleProviderInterface {
            /**
             * @param list<Interval>|null $intervals
             */
            public function __construct(private readonly ?array $intervals)
            {
            }

            public function getLocations(): array
            {
                return [1 => 'Museum'];
            }

            public function getOpenIntervals(int $locationId, DateTimeImmutable $from, DateTimeImmutable $to): ?array
            {
                return $this->intervals;
            }
        };
    }

    private function at(string $time): DateTimeImmutable
    {
        return new DateTimeImmutable($time, $this->tz);
    }
}

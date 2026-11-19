<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Screen;

use DateTimeImmutable;

final readonly class ScreenState
{
    /** Override aus den Seiteneinstellungen */
    public const REASON_OVERRIDE = 'override';
    /** Nach Öffnungszeiten (inkl. Versatz) */
    public const REASON_SCHEDULE = 'schedule';
    /** Kein Ort gewählt */
    public const REASON_NO_LOCATION = 'no_location';
    /** Ort ohne Plan oder kein Öffnungszeiten-Modul installiert */
    public const REASON_UNKNOWN = 'unknown';

    /**
     * @param bool                   $active     true = normaler Inhalt, false = Alternativinhalt
     * @param DateTimeImmutable|null $nextChange nächster Wechsel; null = keiner absehbar
     */
    public function __construct(
        public bool $active,
        public string $reason,
        public ?DateTimeImmutable $nextChange = null,
    ) {
    }
}

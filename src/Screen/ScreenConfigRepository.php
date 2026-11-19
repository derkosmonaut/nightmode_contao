<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Screen;

use Doctrine\DBAL\Connection;

/**
 * Liest die Nachtmodus-Einstellungen einer Seite und vererbt sie über den Seitenbaum.
 *
 * - Einstellungen (Ort, Versatz, Alternativinhalt): die nächste Seite nach oben mit "Einstellungen festlegen"
 *   liefert alle Werte. So bilden Elternseiten einen Raum, dessen Screens die Werte übernehmen.
 * - Override: der nächste gesetzte Wert nach oben gewinnt, unabhängig davon, wo die Einstellungen herkommen.
 *   Ein Override am Raum schaltet so alle Screens darin, solange ein Screen keinen eigenen hat.
 */
class ScreenConfigRepository
{
    /** Schutz gegen fehlerhafte Seitenbäume (Zyklen) */
    private const MAX_DEPTH = 50;

    /** @var array<int, ScreenConfig|null> */
    private array $cache = [];

    public function __construct(private readonly Connection $connection)
    {
    }

    public function findForPage(int $pageId): ?ScreenConfig
    {
        if (\array_key_exists($pageId, $this->cache)) {
            return $this->cache[$pageId];
        }

        $settings = null;
        $override = ScreenConfig::OVERRIDE_NONE;
        $overridePageId = 0;
        $id = $pageId;

        for ($depth = 0; $id > 0 && $depth < self::MAX_DEPTH; ++$depth) {
            $row = $this->connection->fetchAssociative(
                'SELECT id, pid, nightmode_set, nightmode_location, nightmode_lead, nightmode_lag, nightmode_column,
                        nightmode_type, nightmode_article, nightmode_page, nightmode_override
                   FROM tl_page WHERE id = ?',
                [$id],
            );

            if (false === $row) {
                break;
            }

            if (0 === $overridePageId && \in_array($row['nightmode_override'], [ScreenConfig::OVERRIDE_ON, ScreenConfig::OVERRIDE_OFF], true)) {
                $override = (string) $row['nightmode_override'];
                $overridePageId = (int) $row['id'];
            }

            if (null === $settings && '' !== (string) $row['nightmode_set']) {
                $settings = $row;
            }

            if (null !== $settings && 0 !== $overridePageId) {
                break;
            }

            $id = (int) $row['pid'];
        }

        if (null === $settings) {
            return $this->cache[$pageId] = null;
        }

        $column = trim((string) $settings['nightmode_column']);

        return $this->cache[$pageId] = new ScreenConfig(
            $pageId,
            (int) $settings['id'],
            (int) $settings['nightmode_location'],
            (int) $settings['nightmode_lead'],
            (int) $settings['nightmode_lag'],
            '' !== $column ? $column : 'main',
            (string) $settings['nightmode_type'] ?: ScreenConfig::TYPE_BLANK,
            (int) $settings['nightmode_article'],
            (int) $settings['nightmode_page'],
            $override,
            $overridePageId,
        );
    }

    /**
     * Merkt sich, wann ein Screen zuletzt geladen wurde. Ändert bewusst nicht tstamp der Seite.
     */
    public function recordHeartbeat(int $pageId, int $time): void
    {
        $this->connection->executeStatement('UPDATE tl_page SET nightmode_seen = ? WHERE id = ?', [$time, $pageId]);
    }

    public function findLastSeen(int $pageId): ?int
    {
        $seen = (int) $this->connection->fetchOne('SELECT nightmode_seen FROM tl_page WHERE id = ?', [$pageId]);

        return $seen > 0 ? $seen : null;
    }
}

<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Screen;

/**
 * Wirksame Einstellungen eines Infoscreens, nach Vererbung im Seitenbaum.
 */
final readonly class ScreenConfig
{
    public const OVERRIDE_NONE = '';
    public const OVERRIDE_ON = 'on';
    public const OVERRIDE_OFF = 'off';

    public const TYPE_ARTICLE = 'article';
    public const TYPE_PAGE = 'page';
    public const TYPE_BLANK = 'blank';

    /**
     * @param int    $pageId          die aufgerufene Seite (der Screen)
     * @param int    $sourcePageId    die Seite, an der die Einstellungen festgelegt sind (sie selbst oder ein Elternelement)
     * @param int    $overridePageId  die Seite, von der der Override stammt (0 = kein Override)
     * @param int    $leadMinutes     so viele Minuten vor der Öffnung einschalten
     * @param int    $lagMinutes      so viele Minuten nach der Schließung ausschalten
     * @param string $column          Layout-Spalte, die umgeschaltet wird
     */
    public function __construct(
        public int $pageId,
        public int $sourcePageId,
        public int $locationId,
        public int $leadMinutes = 0,
        public int $lagMinutes = 0,
        public string $column = 'main',
        public string $alternativeType = self::TYPE_BLANK,
        public int $alternativeArticle = 0,
        public int $alternativePage = 0,
        public string $override = self::OVERRIDE_NONE,
        public int $overridePageId = 0,
    ) {
    }
}

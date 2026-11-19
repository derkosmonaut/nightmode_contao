<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\EventListener;

use Contao\Config;
use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Contao\DataContainer;
use Contao\Date;
use Contao\System;
use DateTimeImmutable;
use DateTimeZone;
use Derkosmonaut\NightmodeBundle\Screen\ScreenConfig;
use Derkosmonaut\NightmodeBundle\Screen\ScreenConfigRepository;
use Derkosmonaut\NightmodeBundle\Screen\ScreenState;
use Derkosmonaut\NightmodeBundle\Screen\ScreenStateResolver;

/**
 * Statusanzeige in den Seiteneinstellungen: was der Screen gerade zeigt, wann er wechselt,
 * woher die Einstellungen stammen und wann er zuletzt geladen wurde.
 */
#[AsCallback(table: 'tl_page', target: 'fields.nightmode_status.input_field_callback')]
class PageStatusListener
{
    public function __construct(
        private readonly ScreenConfigRepository $configs,
        private readonly ScreenStateResolver $resolver,
    ) {
    }

    public function __invoke(DataContainer $dc): string
    {
        System::loadLanguageFile('tl_page');
        $lang = $GLOBALS['TL_LANG']['tl_page']['nightmode_info'] ?? [];

        $config = $this->configs->findForPage((int) $dc->id);

        $html = '<div class="widget clr"><h3>'.$this->e($lang['title'] ?? 'Status (gespeicherter Stand)').'</h3>';

        if (null === $config) {
            return $html.'<p class="tl_help">'.$this->e($lang['none'] ?? 'Für diese Seite ist kein Infoscreen eingerichtet.').'</p></div>';
        }

        $now = new DateTimeImmutable('now', new DateTimeZone(date_default_timezone_get()));
        $state = $this->resolver->resolve($config, $now);
        $format = (string) Config::get('datimFormat');

        $rows = [];
        $rows[] = [
            $lang['state'] ?? 'Jetzt',
            $state->active ? ($lang['active'] ?? 'normaler Inhalt') : ($lang['inactive'] ?? 'Alternativinhalt'),
        ];
        $rows[] = [$lang['reason'] ?? 'Grund', $lang['reasons'][$state->reason] ?? $state->reason];

        if (ScreenState::REASON_SCHEDULE === $state->reason) {
            $rows[] = [
                $lang['next'] ?? 'Nächster Wechsel',
                null !== $state->nextChange ? Date::parse($format, $state->nextChange->getTimestamp()) : ($lang['never'] ?? 'keiner absehbar'),
            ];
        }

        if ($config->sourcePageId !== $config->pageId) {
            $rows[] = [$lang['source'] ?? 'Einstellungen von', \sprintf($lang['page'] ?? 'Seite ID %d', $config->sourcePageId)];
        }

        if (ScreenConfig::OVERRIDE_NONE !== $config->override && $config->overridePageId !== $config->pageId) {
            $rows[] = [$lang['overrideSource'] ?? 'Override von', \sprintf($lang['page'] ?? 'Seite ID %d', $config->overridePageId)];
        }

        $seen = $this->configs->findLastSeen($config->pageId);
        $rows[] = [$lang['seen'] ?? 'Zuletzt geladen', null !== $seen ? Date::parse($format, $seen) : ($lang['neverSeen'] ?? 'noch nie')];

        $html .= '<table class="tl_show"><tbody>';

        foreach ($rows as [$label, $value]) {
            $html .= '<tr><td class="tl_label">'.$this->e((string) $label).'</td><td>'.$this->e((string) $value).'</td></tr>';
        }

        return $html.'</tbody></table></div>';
    }

    private function e(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

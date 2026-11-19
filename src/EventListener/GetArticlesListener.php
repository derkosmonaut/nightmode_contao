<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsHook;
use DateTimeImmutable;
use DateTimeZone;
use Derkosmonaut\NightmodeBundle\Screen\AlternativeRenderer;
use Derkosmonaut\NightmodeBundle\Screen\ScreenConfigRepository;
use Derkosmonaut\NightmodeBundle\Screen\ScreenStateResolver;

/**
 * Ersetzt außerhalb der Öffnungszeiten die Artikel der eingestellten Layout-Spalte durch den Alternativinhalt.
 * Die Artikel selbst bleiben unverändert, entschieden wird bei jedem Aufruf.
 */
#[AsHook('getArticles')]
class GetArticlesListener
{
    public function __construct(
        private readonly ScreenConfigRepository $configs,
        private readonly ScreenStateResolver $resolver,
        private readonly AlternativeRenderer $renderer,
    ) {
    }

    public function __invoke(int $pageId, string $column): ?string
    {
        $config = $this->configs->findForPage($pageId);

        if (null === $config || $column !== $config->column) {
            return null;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone(date_default_timezone_get()));

        if ($this->resolver->resolve($config, $now)->active) {
            return null; // normale Artikel
        }

        return $this->renderer->render($config, $column);
    }
}

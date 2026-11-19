<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\EventListener;

use Contao\CoreBundle\Security\Authentication\Token\TokenChecker;
use Contao\PageModel;
use Derkosmonaut\NightmodeBundle\Screen\ScreenConfigRepository;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Für Infoscreen-Seiten:
 * - Antwort nie cachen, damit Umschaltung, spontane Schließung und Override beim nächsten Reload greifen
 * - "zuletzt gesehen" speichern (Heartbeat), außer in der Backend-Vorschau
 *
 * Niedrige Priorität, damit die Cache-Header von Contao nicht wieder überschrieben werden.
 */
#[AsEventListener(event: KernelEvents::RESPONSE, priority: -2048)]
class ScreenResponseListener
{
    public function __construct(
        private readonly ScreenConfigRepository $configs,
        private readonly TokenChecker $tokenChecker,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $page = $request->attributes->get('pageModel');

        if (!$page instanceof PageModel) {
            return;
        }

        if (null === $this->configs->findForPage((int) $page->id)) {
            return;
        }

        $response = $event->getResponse();
        $response->setPrivate();
        $response->setMaxAge(0);
        $response->headers->addCacheControlDirective('no-store');
        $response->headers->addCacheControlDirective('must-revalidate');

        if ($request->isMethod('GET') && $response->isSuccessful() && !$this->tokenChecker->isPreviewMode()) {
            $this->configs->recordHeartbeat((int) $page->id, time());
        }
    }
}

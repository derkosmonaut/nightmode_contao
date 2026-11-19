<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\Screen;

use Contao\ArticleModel;
use Contao\Controller;
use Twig\Environment;

/**
 * Erzeugt den Alternativinhalt für eine Layout-Spalte.
 */
class AlternativeRenderer
{
    public function __construct(private readonly Environment $twig)
    {
    }

    public function render(ScreenConfig $config, string $column): string
    {
        $content = match ($config->alternativeType) {
            ScreenConfig::TYPE_ARTICLE => $this->renderArticle($config->alternativeArticle, $column),
            ScreenConfig::TYPE_PAGE => $this->renderPage($config->alternativePage, $column),
            default => '',
        };

        // Ist der gewählte Inhalt leer (gelöscht, unveröffentlicht), fällt der Screen auf schwarz zurück
        $blank = ScreenConfig::TYPE_BLANK === $config->alternativeType || '' === trim($content);

        return $this->twig->render('@Contao/nightmode/alternative.html.twig', [
            'content' => $content,
            'blank' => $blank,
            'type' => $config->alternativeType,
        ]);
    }

    private function renderArticle(int $articleId, string $column): string
    {
        if ($articleId < 1) {
            return '';
        }

        // dritter Parameter: wie ein Insert-Tag, damit auch Artikel anderer Seiten erlaubt sind
        $html = Controller::getArticle($articleId, false, true, $column);

        return \is_string($html) ? $html : '';
    }

    private function renderPage(int $pageId, string $column): string
    {
        if ($pageId < 1) {
            return '';
        }

        $articles = ArticleModel::findPublishedByPidAndColumn($pageId, $column);

        if (null === $articles) {
            return '';
        }

        $html = '';

        foreach ($articles as $article) {
            $html .= $this->renderArticle((int) $article->id, $column);
        }

        return $html;
    }
}

<?php

declare(strict_types=1);

namespace Derkosmonaut\NightmodeBundle\EventListener;

use Contao\CoreBundle\DependencyInjection\Attribute\AsCallback;
use Derkosmonaut\NightmodeBundle\Schedule\ScheduleProviderInterface;
use Doctrine\DBAL\Connection;

/**
 * Auswahllisten für die Nachtmodus-Felder an tl_page.
 */
class PageOptionsListener
{
    public function __construct(
        private readonly ScheduleProviderInterface $provider,
        private readonly Connection $connection,
    ) {
    }

    /**
     * @return array<int, string>
     */
    #[AsCallback(table: 'tl_page', target: 'fields.nightmode_location.options')]
    public function locations(): array
    {
        return $this->provider->getLocations();
    }

    /**
     * Alle Artikel, nach Seite gruppiert.
     *
     * @return array<string, array<int, string>>
     */
    #[AsCallback(table: 'tl_page', target: 'fields.nightmode_article.options')]
    public function articles(): array
    {
        $rows = $this->connection->fetchAllAssociative(
            'SELECT a.id, a.title, a.inColumn, p.title AS page_title, p.id AS page_id
               FROM tl_article a
         INNER JOIN tl_page p ON p.id = a.pid
           ORDER BY p.title, a.sorting',
        );

        $options = [];

        foreach ($rows as $row) {
            $group = \sprintf('%s (ID %d)', $row['page_title'], $row['page_id']);
            $options[$group][(int) $row['id']] = \sprintf('%s [%s]', $row['title'], $row['inColumn']);
        }

        return $options;
    }
}

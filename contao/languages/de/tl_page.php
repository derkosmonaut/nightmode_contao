<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_page']['nightmode_legend'] = 'Infoscreen (Nachtmodus)';

$GLOBALS['TL_LANG']['tl_page']['nightmode_set'] = ['Infoscreen-Einstellungen festlegen', 'Legt die Einstellungen für diese Seite und alle Unterseiten fest. Ohne Haken übernimmt die Seite die Einstellungen der nächsten Elternseite, die welche festlegt.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_location'] = ['Ort', 'Nach den Öffnungszeiten dieses Ortes schaltet der Screen um. Spontane Schließungen, Ausnahmen und Feiertage zählen mit.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_lead'] = ['Vorlauf (Minuten)', 'So viele Minuten vor der Öffnung zeigt der Screen schon den normalen Inhalt.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_lag'] = ['Nachlauf (Minuten)', 'So viele Minuten nach der Schließung zeigt der Screen noch den normalen Inhalt.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_column'] = ['Layout-Spalte', 'Die Spalte, deren Artikel ersetzt werden, meist „main“.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_type'] = ['Alternativinhalt', 'Was außerhalb der Öffnungszeiten angezeigt wird.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_article'] = ['Artikel', 'Dieser Artikel wird angezeigt. Er darf auf einer anderen Seite liegen.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_page'] = ['Seite', 'Die veröffentlichten Artikel dieser Seite (gleiche Layout-Spalte) werden angezeigt.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_bridge'] = ['Pausen überbrücken', 'Hat ein Tag mehrere Zeitfenster (z. B. 9-12 und 13-17 Uhr), bleibt der Screen von der ersten Öffnung bis zur letzten Schließung durchgehend an.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_override'] = ['Manuell schalten', 'Überstimmt die Öffnungszeiten, z. B. bei Veranstaltung oder Wartung. Gilt auch für Unterseiten auf „Automatisch“.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_status'] = ['Status', ''];

$GLOBALS['TL_LANG']['tl_page']['nightmode_types'] = [
    'blank' => 'Schwarzer Screen',
    'article' => 'Artikel',
    'page' => 'Inhalt einer anderen Seite',
];

$GLOBALS['TL_LANG']['tl_page']['nightmode_overrides'] = [
    'auto' => 'Automatisch (nach Öffnungszeiten)',
    'on' => 'Immer normalen Inhalt zeigen',
    'off' => 'Immer Alternativinhalt zeigen',
];

$GLOBALS['TL_LANG']['tl_page']['nightmode_info'] = [
    'title' => 'Status (gespeicherter Stand)',
    'none' => 'Für diese Seite ist kein Infoscreen eingerichtet, auch nicht über eine Elternseite.',
    'state' => 'Jetzt',
    'active' => 'normaler Inhalt',
    'inactive' => 'Alternativinhalt',
    'reason' => 'Grund',
    'reasons' => [
        'override' => 'manuell geschaltet',
        'schedule' => 'Öffnungszeiten',
        'no_location' => 'kein Ort gewählt',
        'unknown' => 'keine Öffnungszeiten bekannt (Ort ohne Zeitplan oder gelöscht)',
    ],
    'next' => 'Nächster Wechsel',
    'never' => 'keiner in den nächsten 14 Tagen',
    'source' => 'Einstellungen von',
    'overrideSource' => 'Manuell geschaltet an',
    'page' => 'Seite ID %d',
    'seen' => 'Zuletzt geladen',
    'neverSeen' => 'noch nie',
];

<?php

declare(strict_types=1);

$GLOBALS['TL_LANG']['tl_page']['nightmode_legend'] = 'Info screen (night mode)';

$GLOBALS['TL_LANG']['tl_page']['nightmode_set'] = ['Define info screen settings', 'Defines the settings for this page and all subpages. Without it, the page inherits the settings of the nearest parent page that defines them.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_location'] = ['Location', 'The screen switches according to the opening hours of this location, including closures, exceptions and holidays.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_lead'] = ['Lead time (minutes)', 'Show the regular content this many minutes before opening.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_lag'] = ['Lag time (minutes)', 'Keep showing the regular content this many minutes after closing.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_column'] = ['Layout column', 'The column whose articles are replaced, usually "main".'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_type'] = ['Alternative content', 'What is shown outside the opening hours.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_article'] = ['Article', 'This article is shown. It may belong to another page.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_page'] = ['Page', 'The published articles of this page (same layout column) are shown.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_bridge'] = ['Bridge breaks', 'If a day has several time windows (e.g. 9-12 and 13-17), the screen stays on from the first opening to the last closing.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_override'] = ['Manual switch', 'Switches regardless of the opening hours, e.g. for an evening event or maintenance. On a parent page it applies to all subpages set to "Automatic". Set it back to "Automatic" afterwards.'];
$GLOBALS['TL_LANG']['tl_page']['nightmode_status'] = ['Status', ''];

$GLOBALS['TL_LANG']['tl_page']['nightmode_types'] = [
    'blank' => 'Black screen',
    'article' => 'Article',
    'page' => 'Content of another page',
];

$GLOBALS['TL_LANG']['tl_page']['nightmode_overrides'] = [
    'auto' => 'Automatic (opening hours)',
    'on' => 'Always show regular content',
    'off' => 'Always show alternative content',
];

$GLOBALS['TL_LANG']['tl_page']['nightmode_info'] = [
    'title' => 'Status (saved state)',
    'none' => 'No info screen is set up for this page, neither directly nor via a parent page.',
    'state' => 'Now',
    'active' => 'regular content',
    'inactive' => 'alternative content',
    'reason' => 'Reason',
    'reasons' => [
        'override' => 'switched manually',
        'schedule' => 'opening hours',
        'no_location' => 'no location selected',
        'unknown' => 'no opening hours known (location without plan or opening hours bundle missing)',
    ],
    'next' => 'Next change',
    'never' => 'none within the next 14 days',
    'source' => 'Settings from',
    'overrideSource' => 'Switched manually on',
    'page' => 'page ID %d',
    'seen' => 'Last loaded',
    'neverSeen' => 'never',
];

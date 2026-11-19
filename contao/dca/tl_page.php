<?php

declare(strict_types=1);

use Contao\CoreBundle\DataContainer\PaletteManipulator;

$dca = &$GLOBALS['TL_DCA']['tl_page'];

// Auswahlfelder mit Unterpaletten
$dca['palettes']['__selector__'][] = 'nightmode_set';
$dca['palettes']['__selector__'][] = 'nightmode_type';

// Zeilen: Ort | Spalte · Vorlauf | Nachlauf · Pausen überbrücken · Alternativinhalt | Artikel/Seite
$dca['subpalettes']['nightmode_set'] = 'nightmode_location,nightmode_column,nightmode_lead,nightmode_lag,nightmode_bridge,nightmode_type';
$dca['subpalettes']['nightmode_type_article'] = 'nightmode_article';
$dca['subpalettes']['nightmode_type_page'] = 'nightmode_page';

// Reguläre Seiten sind die Screens, Ordnerseiten eignen sich als Räume
foreach (['regular', 'folder'] as $type) {
    if (!isset($dca['palettes'][$type])) {
        continue;
    }

    PaletteManipulator::create()
        ->addLegend('nightmode_legend', 'cache_legend', PaletteManipulator::POSITION_BEFORE, true)
        ->addField(['nightmode_set', 'nightmode_override', 'nightmode_status'], 'nightmode_legend', PaletteManipulator::POSITION_APPEND)
        ->applyToPalette($type, 'tl_page')
    ;
}

$dca['fields']['nightmode_set'] = [
    'inputType' => 'checkbox',
    'eval' => ['submitOnChange' => true, 'tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$dca['fields']['nightmode_location'] = [
    'inputType' => 'select',
    // Optionen: PageOptionsListener::locations()
    'eval' => ['includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50 clr'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$dca['fields']['nightmode_lead'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'maxval' => 720, 'tl_class' => 'w50 clr'],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

$dca['fields']['nightmode_lag'] = [
    'inputType' => 'text',
    'eval' => ['rgxp' => 'natural', 'maxval' => 720, 'tl_class' => 'w50'],
    'sql' => 'smallint(5) unsigned NOT NULL default 0',
];

$dca['fields']['nightmode_bridge'] = [
    'inputType' => 'checkbox',
    'eval' => ['tl_class' => 'clr'],
    'sql' => ['type' => 'boolean', 'default' => false],
];

$dca['fields']['nightmode_column'] = [
    'inputType' => 'text',
    'default' => 'main',
    'eval' => ['mandatory' => true, 'maxlength' => 64, 'tl_class' => 'w50'],
    'sql' => "varchar(64) NOT NULL default 'main'",
];

$dca['fields']['nightmode_type'] = [
    'inputType' => 'select',
    'default' => 'blank',
    'options' => ['blank', 'article', 'page'],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['nightmode_types'],
    'eval' => ['submitOnChange' => true, 'tl_class' => 'w50 clr'],
    'sql' => "varchar(16) NOT NULL default 'blank'",
];

$dca['fields']['nightmode_article'] = [
    'inputType' => 'select',
    // Optionen: PageOptionsListener::articles()
    'eval' => ['mandatory' => true, 'includeBlankOption' => true, 'chosen' => true, 'tl_class' => 'w50'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$dca['fields']['nightmode_page'] = [
    'inputType' => 'pageTree',
    'foreignKey' => 'tl_page.title',
    'eval' => ['mandatory' => true, 'fieldType' => 'radio', 'tl_class' => 'w50'],
    'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

$dca['fields']['nightmode_override'] = [
    'inputType' => 'select',
    'default' => 'auto',
    // auto = nach Öffnungszeiten (bzw. wie die Elternseite), on/off = manuell geschaltet
    'options' => ['auto', 'on', 'off'],
    'reference' => &$GLOBALS['TL_LANG']['tl_page']['nightmode_overrides'],
    'eval' => ['tl_class' => 'w50 clr'],
    'sql' => "varchar(8) NOT NULL default 'auto'",
];

// Statusanzeige: PageStatusListener
$dca['fields']['nightmode_status'] = [
    'eval' => ['doNotShow' => true],
];

// Heartbeat, wird beim Laden der Seite gesetzt
$dca['fields']['nightmode_seen'] = [
    'eval' => ['doNotCopy' => true],
    'sql' => 'int(10) unsigned NOT NULL default 0',
];

unset($dca);

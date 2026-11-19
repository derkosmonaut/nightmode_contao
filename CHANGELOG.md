# Changelog

## [Unreleased]

- Anleitung im README, Screenshot der Seiteneinstellungen

## [0.1] – 2026-11-19 (Release Candidate)

Erste Version.

- Infoscreen-Einstellungen an Seiten (reguläre Seiten und Ordnerseiten), vererbbar über den Seitenbaum
- Umschaltung über den Hook `getArticles`: Außerhalb der Öffnungszeiten ersetzt der Alternativinhalt die Artikel
  einer Layout-Spalte
- Alternativinhalt: schwarzer Screen, ein Artikel (auch von anderen Seiten) oder der Inhalt einer anderen Seite
- Vor- und Nachlauf in Minuten
- Option „Pausen überbrücken“ für mehrere Zeitfenster am Tag
- „Manuell schalten“: Automatisch / Immer normalen Inhalt zeigen / Immer Alternativinhalt zeigen, vererbbar
- Öffnungszeiten aus `derkosmonaut/opentimes-contao` (feste Abhängigkeit)
- Infoscreen-Seiten werden nie gecacht; „Zuletzt geladen“ pro Screen
- Statusanzeige in den Seiteneinstellungen

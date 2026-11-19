# Changelog

## [Unreleased]

### Hinzugefügt

- Infoscreen-Einstellungen an Seiten (reguläre Seiten und Ordnerseiten), vererbbar über den Seitenbaum
- Umschaltung über den Hook `getArticles`: außerhalb der Öffnungszeiten ersetzt der Alternativinhalt die Artikel einer Layout-Spalte
- Alternativinhalt: schwarzer Screen, Artikel (auch von anderen Seiten) oder Inhalt einer anderen Seite
- Vor- und Nachlauf in Minuten
- Override „immer an“ / „immer aus“, vererbbar (z. B. für einen ganzen Raum)
- Anbindung an `derkosmonaut/opentimes-contao` über `ScheduleProviderInterface`
- Infoscreen-Seiten werden nie gecacht; Heartbeat „zuletzt geladen“
- Statusanzeige in den Seiteneinstellungen

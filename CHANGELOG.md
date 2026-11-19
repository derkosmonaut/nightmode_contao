# Changelog

## [Unreleased]

### Behoben

- Vererbung: Unterseiten ohne eigene Einstellungen übernahmen die der Elternseite nicht. Außerdem wurde jede Seite
  der Installation als Infoscreen behandelt (kein Cache, Heartbeat bei jedem Aufruf).

### Geändert

- `derkosmonaut/opentimes-contao` ist jetzt feste Abhängigkeit und wird mitinstalliert

- Override heißt jetzt „Manuell schalten“ mit der Option „Automatisch (nach Öffnungszeiten)“ als Standard

### Hinzugefügt

- Infoscreen-Einstellungen an Seiten (reguläre Seiten und Ordnerseiten), vererbbar über den Seitenbaum
- Umschaltung über den Hook `getArticles`: außerhalb der Öffnungszeiten ersetzt der Alternativinhalt die Artikel einer Layout-Spalte
- Alternativinhalt: schwarzer Screen, Artikel (auch von anderen Seiten) oder Inhalt einer anderen Seite
- Vor- und Nachlauf in Minuten
- Option „Pausen überbrücken“ für mehrere Zeitfenster am Tag
- Override „immer an“ / „immer aus“, vererbbar (z. B. für einen ganzen Raum)
- Öffnungszeiten aus `derkosmonaut/opentimes-contao` über `ScheduleProviderInterface`
- Infoscreen-Seiten werden nie gecacht; Heartbeat „zuletzt geladen“
- Statusanzeige in den Seiteneinstellungen

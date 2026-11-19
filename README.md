# nightmode_contao

*** In Entwicklung: Grundgerüst, noch nicht in einer Contao-Installation getestet ***

Nachtmodus für Infomonitore in Contao (Bundle für Contao 5.7 oder neuer, PHP 8.5): Eine Seite zeigt ihre Artikel
nur während der Öffnungszeiten. Außerhalb davon erscheint ein Alternativinhalt. Die Artikel selbst bleiben
unverändert, entschieden wird bei jedem Aufruf der Seite. Ein Cronjob ist nicht nötig.

Die Öffnungszeiten kommen aus [opentimes_contao](https://github.com/derkosmonaut/opentimes_contao). Spontane
Schließungen, Ausnahmen, Feiertage und Saisonzeiten gelten damit automatisch auch für die Screens.

## Funktionsumfang

- Ein Screen ist eine Contao-Seite mit eigener URL
- Einstellungen werden im Seitenbaum vererbt: Eine Elternseite (z. B. eine Ordnerseite „Foyer“) bildet einen Raum,
  ihre Unterseiten übernehmen Ort, Versatz und Alternativinhalt
- Alternativinhalt: schwarzer Screen, ein Artikel (auch von einer anderen Seite) oder der Inhalt einer anderen Seite
- Vorlauf und Nachlauf in Minuten (z. B. 15 Minuten vor Öffnung an)
- Pausen überbrücken (optional): bei mehreren Zeitfenstern am Tag durchgehend an
- Manuell schalten: „Automatisch“, „Immer normalen Inhalt zeigen“, „Immer Alternativinhalt zeigen“. An einer
  Elternseite gilt es für alle Unterseiten, die auf „Automatisch“ stehen
- Infoscreen-Seiten werden nie gecacht, damit Änderungen beim nächsten Reload greifen
- „Zuletzt geladen“ pro Screen und Statusanzeige in den Seiteneinstellungen

## Regeln

1. Manuell schalten schlägt alles
2. Kein Ort gewählt oder keine Öffnungszeiten bekannt → normaler Inhalt (lieber Inhalt als ein dunkler Screen)
3. Sonst nach Öffnungszeiten, erweitert um Vor- und Nachlauf. Mit „Pausen überbrücken“ bleibt der Screen von der
   ersten Öffnung bis zur letzten Schließung des Tages an. Ohne die Option schaltet er in Pausen um, außer die
   Pause ist kürzer als Vor- plus Nachlauf.

Ist der gewählte Alternativinhalt leer (gelöscht, unveröffentlicht), zeigt der Screen schwarz.

## Anwendung

1. Unter „Inhalte → Öffnungszeiten“ den Ort anlegen (opentimes_contao).
2. Im Seitenbaum, z. B. an einer Ordnerseite pro Raum, „Infoscreen-Einstellungen festlegen“ aktivieren, Ort und
   Alternativinhalt wählen.
3. Die Screen-Seiten darunter anlegen. Sie übernehmen die Einstellungen, können aber eigene festlegen.
4. Nach der Installation einmal den Cache leeren, damit bereits gecachte Seiten nicht weiter ausgeliefert werden.

Die Seiten müssen sich selbst neu laden, z. B. per `<meta http-equiv="refresh" content="90">` im Layout.

## Hinweise

- Der Schwarz-Screen legt eine Fläche über die ganze Seite. Kopf- und Fußbereich des Layouts bleiben darunter
  geladen, sind aber nicht sichtbar.
- Ein Video als Alternativinhalt startet bei jedem Reload neu. Für solche Screens den Reload länger setzen.
- Gebaut für das klassische Seitenlayout. Ob der Hook `getArticles` auch bei den neuen
  Twig-Layouts von Contao 5.7 greift, ist noch zu testen.

## Technik

- `Screen\ScreenStateResolver`: reine Logik, mit Tests abgedeckt
- `Schedule\ScheduleProviderInterface`: Quelle der Öffnungszeiten. Ohne opentimes_contao ist ein leerer Anbieter
  aktiv, die Screens zeigen dann immer den normalen Inhalt.
- `EventListener\GetArticlesListener`: ersetzt die Artikel der Spalte
- `EventListener\ScreenResponseListener`: kein Cache, Heartbeat

## Lizenz

GPL-3.0-or-later

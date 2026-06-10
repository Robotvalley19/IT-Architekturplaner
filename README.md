# IT-Architekturplaner
<<<<<<< HEAD

Der IT-Architekturplaner ist ein lokales PHP-Projekt zur Planung und zum besseren Verstaendnis von IT-Infrastrukturen in kleinen und mittleren Unternehmen, besonders mit Blick auf Produktionsumgebungen, OT-Netze, Server, VLANs, Backup, Monitoring und Betrieb.

Das Projekt wurde erstellt, um sich mehr mit dem Thema Aufbau einer IT-Infrastruktur zu befassen, dabei praktisch zu lernen und gleichzeitig ein Tool zu bauen, mit dem das Gelernte direkt angewendet werden kann. Andere Firmen, Lernende und technische Entscheider koennen sich an den Strukturen, Beispielen und Planungslogiken orientieren.

## Ziele

- IT-Infrastruktur uebersichtlich planen und erklaeren
- Eingaben wie Mitarbeiterzahl, Gebaeude, Maschinen, Datenmenge und Sicherheitsniveau in konkrete Empfehlungen umwandeln
- Netzwerk, Server, Storage, Backup, Monitoring und Security gemeinsam betrachten
- Lerninhalte sichtbar machen, damit Entscheidungen nachvollziehbar bleiben
- Eine lokale, rechtssichere Projektbasis ohne externe Layout-Abhaengigkeiten bereitstellen

## Lokale und externe Abhaengigkeiten

Das Projekt ist bewusst lokal gehalten.

- Keine Google Fonts
- Keine CDN-Abhaengigkeiten
- Keine extern geladenen Stylesheets oder Skripte
- Keine externen Bildquellen
- Layout und Logik liegen im Projektordner

Verwendete lokale Bestandteile:

- PHP-Dateien in `modules/`
- Konfigurationsdateien in `config/`
- Lokale Styles in `assets/css/`
- Lokale Bilddatei `MarthInnovate.png`


## Haftung und Nutzung auf eigene Gefahr

Die Nutzung dieser Software erfolgt ausschliesslich auf eigene Gefahr. Das Projekt dient als Lern-, Planungs- und Orientierungshilfe und ersetzt keine fachliche Beratung, keine Sicherheitspruefung, keine rechtliche Pruefung und keine verbindliche technische Planung.

Robotvalley19 uebernimmt keine Haftung fuer Fehler, Ausfaelle, Datenverluste, Sicherheitsluecken, Fehlplanungen, wirtschaftliche Schaeden, Folgeschaeden oder sonstige Nachteile, die direkt oder indirekt durch die Nutzung, Anpassung oder Weitergabe dieser Software entstehen. Es besteht kein Anspruch auf Schadenersatz, Gewaehrleistung, Support, Fehlerbehebung oder Aktualisierung.

Vor einem produktiven Einsatz muessen alle Ergebnisse eigenverantwortlich geprueft und an die realen technischen, organisatorischen, rechtlichen und sicherheitsrelevanten Anforderungen angepasst werden.

## Voraussetzungen

- PHP 8 oder neuer
- Ein Browser
- Optional: Git fuer Versionsverwaltung

Es wird keine Datenbank benoetigt.

## Starten

Im Projektordner ausfuehren:

```bash
php -S 127.0.0.1:8080
```

Danach im Browser die lokale Adresse oeffnen:

```text
127.0.0.1:8080
```

Alternativ steht in `start.txt` der einfache Startbefehl.

## Projektstruktur

```text
IT-Architekturplaner/
├── assets/
│   ├── css/
│   ├── img/
│   └── js/
├── config/
├── exports/
├── modules/
├── projects/
├── templates/
├── index.php
├── install_it_arch.sh
├── LICENSE
├── logo.png
├── README.md
└── start.txt
```

## Wichtige Module

- `modules/dashboard.php`: Baut die sichtbare Oberflaeche und verbindet die Planungsdaten.
- `modules/company.php`: Enthaltene Eingabewerte und Standardwerte fuer ein Beispielunternehmen.
- `modules/planning.php`: Zentrale Planungslogik fuer Netzwerk, Server, Storage, Kosten, Monitoring und Umsetzung.
- `modules/firewall.php`: Optionen fuer Firewall- und Sicherheitskomponenten.
- `modules/internet.php`: Internet- und WAN-Optionen.
- `modules/servers.php`: Serverrollen und Basisdienste.
- `modules/buildings.php`: Gebaeude- und Standortlogik.

## Beispiel: Projekt nutzen

1. Projekt lokal starten.
2. Unternehmenswerte im Formular anpassen.
3. Empfehlungen fuer Netzwerk, VLANs, Server, Backup, Monitoring und Umsetzung lesen.
4. Ergebnisse als Grundlage fuer ein echtes Infrastrukturkonzept verwenden.
5. Annahmen pruefen und mit realen Anforderungen, Budget und Sicherheitsvorgaben abgleichen.

Beispielhafte Fragen, die mit dem Tool betrachtet werden koennen:

- Wie viele VLANs braucht ein Unternehmen mit Buero, Produktion und Gastnetz?
- Wo sollten Firewall, Core-Switches, Access-Switches und Server stehen?
- Welche Serverrollen sind fuer Betrieb, Backup und Monitoring sinnvoll?
- Welche Punkte muessen bei OT, CNC, SPS und Robotern getrennt betrachtet werden?
- Wie wirken sich Mitarbeiterzahl, Wachstum und Datenmenge auf Hardware und Kosten aus?

## Weiterentwicklung

Das Projekt kann schrittweise erweitert werden, ohne die Grundstruktur zu verlieren.

Sinnvolle naechste Schritte:

- Speichern und Laden von Projekten in `projects/`
- Export als PDF oder HTML-Bericht in `exports/`
- Eigene Hardware-Kataloge in `config/hardware.php`
- Erweiterte Kostenmodelle fuer Lizenzen, Wartung, Strom und Dienstleistung
- Rollen- und Rechtemodell fuer Admins, Dienstleister und Fachabteilungen
- Checklisten fuer Inbetriebnahme, Backup-Tests, Firewall-Regeln und Dokumentation
- Vorlagen fuer unterschiedliche Unternehmenstypen in `templates/`
- Mehrsprachigkeit fuer deutsche und englische Dokumentation

## Entwicklungsleitlinien

- Externe Abhaengigkeiten nur aufnehmen, wenn sie wirklich notwendig sind.
- Bei neuen Abhaengigkeiten immer Lizenz, Quelle und Zweck dokumentieren.
- Keine Fonts, Skripte oder Bilder direkt von fremden Servern laden.
- Fachliche Regeln lieber in eigenen Funktionen kapseln, damit sie nachvollziehbar bleiben.
- Beispiele realistisch halten und Annahmen offen benennen.
- Aenderungen in kleinen Schritten versionieren.

## Beispiel fuer einen Git-Workflow

```bash
git init
git add .
git commit -m "Initialer IT-Architekturplaner"
```

Danach kann das Repository mit einem eigenen GitHub-Projekt verbunden werden.

## Lizenz

Dieses Projekt steht unter der MIT-Lizenz. Copyright 2026 Robotvalley19. Details stehen in der Datei `LICENSE`. Die Nutzung erfolgt auf eigene Gefahr und ohne Anspruch auf Schadenersatz bei Fehlern in der Software.
# IT-Architekturplaner
=======
>>>>>>> 6dc5c55747b26d89da23095806e6383200f9484d

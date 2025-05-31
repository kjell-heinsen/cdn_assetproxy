# CDN Assetproxy

Dieses kleine Projekt stellt einen CDN Assetproxy dar. Damit sollen die Javascript und CSS Assets vor externer Nutzung geschützt werden.
Das heißt, es kommen nur Domains an die Assets die von dir ausgewählt wurden und in die Config geschrieben wurden. 

## Features

- ✅ AdminLTE 4 Assets
- ✅ Referer Schutz
- ✅ Pfad-Traversal Schutz
- ✅ Praktisches Beispiel

## Installation

1. Dateien in den Webspace hochladen
2. Den Ordner /index als Root - Ordner auswählen
3. sample_config.php in config.php umbenennen und eigene Domains in die Variable für Allowed Domains eintragen

## Systemanforderungen

- PHP 8.1+

## Features

### AdminLTE 4 Assets
Enthalten sind die Kern-Assets von AdminLTE v3 https://github.com/ColorlibHQ/AdminLTE/tree/v3

### Referer Schutz
Daten können nur von bestimmten Domains angesteuert werden

### Pfad-Traversal Schutz
Schutz vor dem Wandern durch verschiedene Pfade

### Praktisches Beispiel 
Beispiele für die Einbindung von CSS und Javascript
```html
<link rel="stylesheet" href="https://cdn.deine-domain.de/index.php?path=css/bootstrap/bootstrap.min.css">
<script src="https://cdn.deine-domain.de/index.php?path=js/vendor/jquery.min.js"></script>
```
## Lizenz
Apache-2.0 license
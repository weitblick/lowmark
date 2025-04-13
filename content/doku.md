---
title: Dokumentation
description: Hier entsteht die Dokumentation
---
# Dokumentation

_Hier entsteht die Dokumentation. Bis jetzt gibt es hier nur ein paar knappe nur Notizen für Redakteure. In Kürze wird lowmark hier zum Download verfügbar und vollständig dokumentiert sein._

## Theme

https://maolonglong.github.io/hugo-simple/markdown-syntax/  
https://github.com/maolonglong/hugo-simple/tree/main/exampleSite

## Frontmatter

* im vereinfachten(!) yaml-Format (---)
* unterstütze Attribute: siehe config.php
* eigene Parameter im Template einbinden:  
  `<?= $lowmark['key'] ?? '' ?>`

##  "Assets"

* Bilder und Download-Dateien liegen im Content-Verzeichnis. Es können Unterverzeichnisse angelegt werden.
* .htaccess enthält eine Regel, welche Dateiendungen berücksichtigt werden und schreibt dann Zugriffe auf diese Dateien um, so dass sie aus dem Content-Verzeichnis geladen werden.
* Dadurch funktionieren eingebettete Bilder und Downloads lokal und auf dem Webserver.
* WICHTIG: Die Pfade müssen relativ sein.
* Bilder brauchen im Standard-Theme eine maximale Breite von 720px.

## Links

* Interne Links können auch auf die .md-Dateien zeigen. Dadurch sind sie auch offline konsistent. Sie werden vom CMS in .html umgeschrieben.
* Externe Links werden automatisch um ein target="_blank" erweitert.
* Ausgeschriebene URLs werden automatisch verlinkt.
* mailencode = true steuert, dass unverlinkte E-Mail-Adressen automatisch verlinkt und verschlüsselt werden.  
  ACHTUNG! Das bedeutet, dass E-Mail-Adressen in der Markdown-Datei nicht verlinkt werden dürfen. Man kann das auch über den Frontmatter individuell pro Seite ein- und ausschalten.

## extended Markdown

### HTML

* Der Markdown-Interpreter ist für HTML-Code transparent

### Inhaltsverzeichnis bzw. "Akkordion"

– entspricht dem HTML-Tag `<details>`

```
<!-- DETAILS Inhalt -->

- [Headings](#headings)
- [Paragraph](#paragraph)
- [Blockquotes](#blockquotes)
- [Tables](#tables)
- ...

<!-- /DETAILS -->
```

### Anker

Sprungziele können an Überschriften nach diesem Schema angehängt werden:

```
## Überschrift{#anker}
```

### Klassen

```
[das ist ein Link](#){.yourClass}

#### das ist eine Überschrift{.yourClass}
```

### Bilder

* :left/right/center am Beginn des Alt-Text sorgt für ein Alignment des Bildes
* title wird auch als Bildunterschrift (figcaption) interpretiert

```
[:left Alt-Text](/images/img.webp "Bildunterschrift")
```



## Syntax-Highlighting

* Frontmatter: `highlight: true`
* Es wird highlight.js und highlight.css eingebunden

## Favicon

* Der Name der Website muss in `touch/site.webmanifest` angepasst werden
* Die Favicons liegen im Verzeichnis `touch/` und können mit https://favicon.io/favicon-generator/ erzeugt werden.
* favicon.ico liegt im Webserver-Root-Verzeichnis

# Installation

* Dateien in Root-VZ kopieren
* index.php anpassen
  * basics
  * ggf. Navigation und Footer
* touch/site.webmanifest anpassen (Titel)!
* Favicons anpassen

## Installation in Subfolder

(e.g. "sub")

* Wichtig! Alle Links relativ ausgehend von der base – also ohne führendes Slash
* in index.php `<base href="https://meine-domain.de/sub/">` ergänzen (gilt auch für editor.php)
* .htaccess anpassen:
  * RewriteBase /sub/
  * in allen RewriteRules /sub/ voranstellen
  * die .htaccess-Datei kommt (natürlich) in den Subfolder!
* image-Pfade im Front-Matter (bzw. $image im Template müssen mit einem Slash beginnen und daher dann "/sub/img/..." heißen)
* Links auf Sprungziele innerhalb derselben Seite müssen (wegen der base) den Dateinamen der Seite enthalten – also z.B. "index.html#anchor" statt "#anchor".

# Editor

Die Arbeit auf der Titelseite ist selbsterklärend. Andere Seiten öffnen oder neu erzeugen (Die Dateien heißen [xyz.md](http://xyz.md) und werden dann als xyz.html angezeigt):

1. Open/New Page klicken
2. Dateinamen links in das Feld eintragen
3. Button "Open/Save" klicken


Jetzt ist die Seite geöffnet, sofern es sie schon gab, bzw. neu angelegt, sofern es sie noch nicht gab. Anschließend Inhalt bearbeiten und auf "Open/Save" klicken, um Änderungen zu speichern.

Du verlinkst dann Seiten untereinander mit \[link](xyz.html) - also relativer Link ohne http oder führenden "/". \[link]([xyz.md](http://xyz.md)) würde übrigens auch gehen.

Wenn Du eine Seite löschen willst, wüdest Du die Seite öffnen wie oben beschrieben und dann, wenn Du den Content der Seite im Editor siehst auf "Delete this Page" klicken. Nach dem Löschen steht der Dateiname noch im Feld und es gibt eine neue leere Seite. Du könntest direkt weiter machen und würdest mit dem nächsten klick auf den "Open/Save" Button die Seite neu anlegen. Das ist i.d.R. aber nicht gewünscht. Um eine andere Seite anzulegen oder zu Öffnen geht es wieder wie oben beschrieben mit "1. Open/New Page klicken" los.

## externe Editoren

* tinyfilemanager.php

## local preview server

- install php (>= 7.0)
- call in project root folder: `php -S localhost:8000 router.php`
- browse http://localhost:8000

# htaccess

Security-Einstellungen: https://observatory.mozilla.org/

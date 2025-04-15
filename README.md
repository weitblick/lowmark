# lowmark alpha (draft)

```
 ███
 ███
 ███    ██████    ██   ██    █████ ████     ██████     ████  ███   ██
 ███  ███    ███  ██   ██   ██  ███  ███  ███    ██   ███    ███  ███
 ███  ███    ███  ███  ███  ██   ██   ██  ███   ████  ███    ███████
 ███    ██████     ████ █████    ██   ██   ██████ ██  ███    ███   ██
```

LOWMARK – A Low-tech Markdown Website Generator

Author: Erhard Maria Klein <emk@lowmark.de>
License: CC BY-NC-SA 4.0
Homepage: https://lowmark.de
Repository: https://github.com/weitblick/lowmark

Depends on:  
Parsedown & ParsedownExtra from https://parsedown.org/  
highlight.js from https://highlightjs.org/

---

_This is where the documentation is created. So far there are only a few brief notes mainly for editors here._


## Frontmatter


* in simplified(!) yaml format (---)
* supported attrributes: see config.php
* Integrate your own extensions in the template:  

  `<?= $lowmark['key'] ?? '' ?>`

## “Assets”

* Images and download files are located in the content directory. Subdirectories can be created.
* .htaccess contains a rule as to which file extensions are taken into account and then rewrites access to these files so that they are loaded from the content directory. This allows embedded images and downloads to work locally and on the web server.
* IMPORTANT: The paths must be relative.
* Images need a maximum width of 720px in the standard theme.

## Links

* Internal links can also point to the .md files. This means that they are also consistent offline. They are rewritten to .html by the CMS.
* External links are automatically extended by a target="_blank".
* URLs that are written out are automatically linked.
* mailencode = true controls that unlinked e-mail addresses are automatically linked and encrypted.  
  ATTENTION: This means that e-mail addresses must not be linked in the Markdown file. You can also switch this on and off individually per page via the frontmatter.

## extended Markdown

### HTML

* The Markdown interpreter is transparent for HTML code

### Table of contents or “accordion”

- corresponds to the HTML tag `<details>`

```
<!-- DETAILS content -->

- Headings](#headings)
- Paragraph](#paragraph)
- Blockquotes](#blockquotes)
- Tables](#tables)
- ...

<!-- /DETAILS -->
```


### Anchor

Jump targets can be attached to headings according to this scheme:

```
## Heading{#anchor}
```


### Classes

```
[this is a link](#){.yourClass}

#### this is a heading{.yourClass}
```


### Images

* :left/right/center at the beginning of the alt text aligns the image
* title is also interpreted as a caption (figcaption)

```
[:left Alt-Text](/images/img.webp "caption")
```

## Syntax highlighting

* Frontmatter: `highlight: true`
* highlight.js and highlight.css from from https://highlightjs.org/ are included


## Favicon

* The name of the website must be adapted in `touch/site.webmanifest`.
* The favicons are located in the `touch/` directory and can be created with https://favicon.io/favicon-generator/.
* favicon.ico is located in the web server root directory

## Installation


* Copy files to server root directory
* Download Parsedown.php (https://github.com/erusev/parsedown) and ParsedownExtra.php (https://github.com/erusev/parsedown-extra) to lib folder
* Customize index.php
  * basics
  * Navigation and footer if necessary
* customize touch/site.webmanifest (title)!
* customize favicons

### Installation in subfolder

(e.g. “sub”)

* Important! All links relative to the base - i.e. without leading slash
* add `<base href="https://meine-domain.de/sub/">` to index.php (also applies to editor.php)
* Adapt .htaccess:
  * RewriteBase /sub/
  * put /sub/ in front in all RewriteRules
  * the .htaccess file (of course) goes into the subfolder “sub”!
* image paths in the front matter (or $image in the template must begin with a slash and therefore be called “/sub/img/...”)
* Links to jump targets within the same page must contain the file name of the page (because of the base) - e.g. “index.html#anchor” instead of “#anchor”.

## local preview server

- install php (>= 7.0)
- call in project root folder: `php -S localhost:8000 router.php`
- browse http://localhost:8000

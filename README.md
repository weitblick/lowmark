# lowmark

```
 ███
 ███
 ███    ██████    ██   ██    █████ ████     ██████     ████  ███   ██
 ███  ███    ███  ██   ██   ██  ███  ███  ███    ██   ███    ███  ███
 ███  ███    ███  ███  ███  ██   ██   ██  ███   ████  ███    ███████
 ███    ██████     ████ █████    ██   ██   ██████ ██  ███    ███   ██
```

**LOWMARK – A Low-tech Markdown Website Generator**

Version: 0.4  
Last updated: 2025-04-16
Homepage: https://lowmark.de
Repository: https://github.com/weitblick/lowmark

Copyright (c) 2025 Erhard Maria Klein, lowmark.de
Licensed under the MIT License
See LICENSE file or https://opensource.org/licenses/MIT

Depends on: Parsedown & ParsedownExtra from https://parsedown.org/

---

## What is lowmark?

**Create websites with ease – using just Markdown.**

- Write and publish your content as simple Markdown files
- This “CMS” is technically just a small PHP script
- No technical skills required – installation and usage take just a few minutes
- The core innovation is its radical simplicity, putting content back at the center
- Inspired by the ideals of the [Lowtech](https://solar.lowtechmagazine.com/), [Slow Media](https://www.slow-media.net/manifest) and [Small Web](https://smallweb.page/home) movements

Our goal is to create a lightweight online space where documents are simply documents — respecting the privacy, attention, and cognitive capacity of every reader, and promoting both mental and ecological sustainability.

> **Lowmark** isn’t about tech – it’s about values.

Learn more about the philosophy behind Lowmark → [lowmark.de/about.html](https://lowmark.de/about.html)

---

## Installation

### 1. Download the repository

#### Option A: Clone via Git

```bash
git clone git clone https://github.com/weitblick/lowmark.git
```

#### Option B: Download ZIP archive

1. Click on **"Code" → "Download ZIP"**
2. Extract the archive to a folder of your choice

> ⚠️ Make sure you're using the contents of the `main` branch.

### 2. Set up your project folder

Copy the contents of the `example_site` folder into the project root directory.  
Ensure that the `config.php` file is placed in the `lowmark` subfolder where the core scripts are located.
Then delete the `example_site` folder.

### 3. Deploy your site

- Upload all files to a web server, **or**
- Use the built-in PHP development server (see below for local preview).

---

Klar! Hier ist dein Verzeichnisbaum im sauberen Markdown-Format, gut lesbar für ein GitHub-README:

Du kannst diesen Abschnitt z. B. als „Project Structure“ oder „Directory Overview“ einfügen:

------

## Project Structure

```
assets/               → CSS, images, and other static assets
content/              → Page content in Markdown format
  └── index.md        → Homepage

livereload/           → Scripts for the local preview mode (optional)
lowmark/              → Core logic of the site generator
  ├── components.php      → Additional features
  ├── config.php          → Base configuration (must be customized!)
  ├── core.php            → Get markdown file and render it to HTML
  ├── frontmatter.php     → Frontmatter parser
  ├── Parsedown.php       → Markdown parser
  └── ParsedownExtra.php  → Extended Markdown support

.htaccess            → URL rewriting for Apache servers
index.php            → Main template file; initializes lowmark by calling core.php
router.php           → Entry point for the local PHP preview server (optional)
```

---

## System Requirements

- **Webserver**: Apache
- **PHP**: Version **8.0** or higher
  - PHP extensions: `GD` or `Imagick` for image scaling (optional but recommended)

------

## Local Preview Server

1. **Install PHP**  
   PHP version 8.0 or higher is required.

2. **Start the server**  
   In the project root folder, run:

   ```bash
   php -S localhost:8000 router.php
   ```

3. **Preview your site**  
   Open your browser and navigate to:
   http://localhost:8000

<?php
/*
 * ███
 * ███
 * ███    ██████    ██   ██    █████ ████     ██████     ████  ███   ██
 * ███  ███    ███  ██   ██   ██  ███  ███  ███    ██   ███    ███  ███
 * ███  ███    ███  ███  ███  ██   ██   ██  ███   ████  ███    ███████
 * ███    ██████     ████ █████    ██   ██   ██████ ██  ███    ███   ██
 *
 * LOWMARK – A Low-tech Markdown Website Generator
 *
 * File:         livereload.php
 * Version:      0.4
 * Last updated: 2025-04-16
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Automatically reloads the site when a content file changes
 *               (for local development only).
 *
 * Usage:        Start with: php -S localhost:8000 router.php
 *               Open in browser: http://localhost:8000
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 */

// ============ LOWMARK WATCH FOR LIVERELOAD ================

// Get the request URL from GET parameter 'q'
$path = isset($_GET['q']) ? $_GET['q'] : 'index';

// a little security (no leading / and no ../)
$path = preg_replace('/((|\/)\.\.\/)+/', '/', $path);
$path = ltrim($path, '/');

// If the URL ends in .html, replace with .md
$mdFile = "../content/" . preg_replace('/\.html$/', '.md', $path);

// If the file exists, return its 'filemtime', otherwise return a fixed timestamp (1.1.1970)
echo file_exists($mdFile) ? filemtime($mdFile) : 0;

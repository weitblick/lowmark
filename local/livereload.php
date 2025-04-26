<?php
/**
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
 * Version:      0.5
 * Last updated: 2025-04-26
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  get filetime and return it to livereload.js
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 *
 * Lowmark is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Lowmark is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Lowmark. If not, see <https://www.gnu.org/licenses/>.
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

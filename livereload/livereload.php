<?php
/*
	██       ██████  ██     ███    ███  █████  ██████  ██   ██
	██      ██    ██ ██     ████  ████ ██   ██ ██   ██ ██  ██
	██      ██    ██ ██  █  ██ ████ ██ ███████ ██████  █████
	██      ██    ██ ██ ███ ██  ██  ██ ██   ██ ██   ██ ██  ██
	███████  ██████   ███ ████      ██ ██   ██ ██   ██ ██   ██

	LOWMARK – A Low-tech Markdown Website Generator
	Version: 0.3 (2025-03-17)
	https://lowmark.de

	by Erhard Maria Klein
	emk@lowmark.de
	CC BY-NC-SA 4.0

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

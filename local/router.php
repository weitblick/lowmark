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
 * File:         router.php
 * Version:      0.5
 * Last updated: 2025-04-23
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Router for local development
 *
 * Usage:        Start with: php -S localhost:8000 local/router.php
 *               Open in browser: http://localhost:8000
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// root directory of the project (parent of /local/)
$root = dirname(__DIR__);

// write local error log to lowmark/php_errors.log
// ini_set('log_errors', 1);
// ini_set('error_log', 'lowmark/php_errors.log');

// Decode current request URL
$request = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Add index.html to URLs with a trailing slash
if (preg_match('#(.+)/$#', $request)) {
	$request = $request . 'index.html';
}

// If a file or directory exists, deliver directly
if (file_exists($root . $request)) {
    return false;
}

// Rewrite requests for non-existing files
// to /content/, but avoid recursion and
// ignore .html

if (!preg_match('#\.html$#', $request) && !preg_match("#^/content/#", $request)) {
    $content_path = $root . '/content' . $request;
    if (file_exists($content_path)) {
        header('Content-Type: ' . mime_content_type($content_path));
        readfile($content_path);
        exit;
    }
}

// The LOWMARK part
$_GET['q'] = ltrim($request, '/');
include $root . '/index.php';

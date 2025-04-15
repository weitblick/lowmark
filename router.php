<?php
/*
 ███
 ███
 ███    ██████    ██   ██    █████ ████     ██████     ████  ███   ██
 ███  ███    ███  ██   ██   ██  ███  ███  ███    ██   ███    ███  ███
 ███  ███    ███  ███  ███  ██   ██   ██  ███   ████  ███    ███████
 ███    ██████     ████ █████    ██   ██   ██████ ██  ███    ███   ██

 LOWMARK – A Low-tech Markdown Website Generator

 File:         router.php
 Author:       Erhard Maria Klein <emk@lowmark.de>
 Version:      0.31
 Last updated: 2025-04-14
 License:      CC BY-NC-SA 4.0
 Homepage:     https://lowmark.de
 Repository:   https://github.com/weitblick/lowmark

 Description:  Router for local execution
               start with: php -S localhost:8000 router.php
               browse: http://localhost:8000
*/

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

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
if (file_exists(__DIR__ . $request)) {
    return false;
}

// Rewrite requests for non-existing files
// to /content/, but avoid recursion and
// ignore .html
if (!preg_match('#\.html$#', $request) && !preg_match('#^/content/#', $request)) {
    $content_path = __DIR__ . '/content' . $request;
    if (file_exists($content_path)) {
        header('Content-Type: ' . mime_content_type($content_path));
        readfile($content_path);
        exit;
    }
}

// The LOWMARK part
$_GET['q'] = ltrim($request, '/');
include 'index.php';

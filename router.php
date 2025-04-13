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

// ============ LOWMARK ROUTER FOR LOCAL EXECUTION ================
//
// start with: php -S localhost:8000 router.php
// browse: http://localhost:8000

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// write local error log to lowmark/php_errors.log
// ini_set('log_errors', 1);
// ini_set('log_errors', 1);
// ini_set('error_log', 'lowmark/php_errors.log');

// Decode current request URL
$request = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If a file or directory exists, deliver directly
if (file_exists(__DIR__ . $request)) {
    return false;
}

// Rewrite images and downloads (PDFs, MP3s, MP4s etc.) to /content/...
// Ignore /touch/
// Add more file extensions if needed
if (!preg_match('#^/content/#i', $request) && !preg_match('#^/touch/#i', $request)) {
    if (preg_match('#\.(jpg|jpeg|svg|gif|png|webp|pdf|mp3|mp4)$#i', $request)) {
        $contentPath = __DIR__ . '/content' . $request;
        if (file_exists($contentPath)) {
            header('Content-Type: ' . mime_content_type($contentPath));
            readfile($contentPath);
            exit;
        }
    }
}

// Add index.html to URLs with a trailing slash
if (preg_match('#(.+)/$#', $request)) {
    $request = $request . 'index.html';
}

// The LOWMARK part
$_GET['q'] = ltrim($request, '/');
include 'index.php';

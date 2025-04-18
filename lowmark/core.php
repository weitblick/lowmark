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
 * File:         core.php
 * Version:      0.4
 * Last updated: 2025-04-14
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Get markdown file and render it to HTML
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 *
 * Depends on:   Parsedown & ParsedownExtra from https://parsedown.org/
 */

$start_time = microtime(true); // Start render time

$lowmark = array(); // Holds environment and page-specific data

// Includes

// Get configuration
if (file_exists('lowmark/config.php')) {
    include_once 'lowmark/config.php';
} else {
    // Fallback if lowmark/config.php is missing
    $lowmark = [
        'content_dir' => 'content/', // Directory for content files (with trailing slash)
        'sitename'    =>  'missing site name'
    ];
}

include_once 'lowmark/frontmatter.php'; // Simple frontmatter parser
include_once 'lowmark/components.php'; // Lowmark components
include_once 'lowmark/Parsedown.php'; // Markdown parser. Download from https://github.com/erusev/parsedown
include_once 'lowmark/ParsedownExtra.php'; // Markdown extra extension. Download from https://github.com/erusev/parsedown-extra

$path = $_GET['q'] ?? 'index.html'; // Get the path from the GET parameter q, default: index.html

// A little security (no leading / and no ../)
$path = preg_replace('/((|\/)\.\.\/)+/', '/', $path);
$path = ltrim($path, '/');

$converted_path = preg_replace('/\.html$/', '.md', $path); // Change .html into .md
$lowmark['home'] = ($converted_path == 'index.md'); // Identify the homepage
$md_file_path = $lowmark['content_dir'] . $converted_path; // Path to markdown file in content directory

if (file_exists($md_file_path) && is_file($md_file_path)) { // Check if the markdown file exists
    $markdown = file_get_contents($md_file_path); // Read the content of the markdown file
    $resource = parse_frontmatter($markdown); // Get metadata (frontmatter and content) from markdown file
    // Merge Frontmatter values into $lowmark
    if (isset($resource['frontmatter']) && is_array($resource['frontmatter'])) {
        $lowmark = array_merge($lowmark, $resource['frontmatter']);
    }

    // Convert markdown content to HTML
    $Extra = new ParsedownExtra();
    if (($Extra instanceof ParsedownExtra)&&(!($lowmark['raw_html'] ?? false))) {
        $lowmark['content'] = $Extra->text($resource['content']); // Markdown parser
    } else {
        $lowmark['content'] = $resource['content']; // Don’t render in markdown
    }

    // Extend Markdown with custom functions
    if ($lowmark['img_to_figure']  ?? false) $lowmark['content'] = img_to_figure($lowmark['content']); // Add <figure> to <img> tags
    if ($lowmark['extend_links']  ?? false)  $lowmark['content'] = extend_links($lowmark['content']); // Extend <a> tags
    if ($lowmark['mail_encode']  ?? false)   $lowmark['content'] = mail_encode($lowmark['content']); // Mail encoding
    if ($lowmark['headline_ids']  ?? false)  [$lowmark['content'], $lowmark['headlines']] = array_values(headline_ids($lowmark['content'], $lowmark['headline_to_top'])); // Add unique ids to headlines and return an array of headlines
    if ($lowmark['details_patch']  ?? false) $lowmark['content'] = details_patch($lowmark['content']); // <details> workaround

} else {
    // If the file does not exist, issue a 404 error
    http_response_code(404);
    $lowmark['title']   = 'Error 404';
    $lowmark['content'] = "<h3>Error 404: Not Found</h3><p>Source: $converted_path</p>";
}

// set base url and canonical url
$lowmark['base_url'] ??= (($_SERVER['HTTPS'] ?? '') === 'on' ? 'https' : 'http') . "://$_SERVER[HTTP_HOST]";
$lowmark['base_url'] = rtrim($lowmark['base_url'], '/');
$lowmark['canonical_url'] = $lowmark['base_url'] . $_SERVER['REQUEST_URI'];

$end_time = microtime(true); // Determine the execution time of the script
$execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds
?>

<?php
/*
 ███
 ███
 ███    ██████    ██   ██    █████ ████     ██████     ████  ███   ██
 ███  ███    ███  ██   ██   ██  ███  ███  ███    ██   ███    ███  ███
 ███  ███    ███  ███  ███  ██   ██   ██  ███   ████  ███    ███████
 ███    ██████     ████ █████    ██   ██   ██████ ██  ███    ███   ██

 LOWMARK – A Low-tech Markdown Website Generator

 File:         core.php
 Author:       Erhard Maria Klein <emk@lowmark.de>
 Version:      0.31
 Last updated: 2025-04-14
 License:      CC BY-NC-SA 4.0
 Homepage:     https://lowmark.de
 Repository:   https://github.com/weitblick/lowmark

 Description:  lowmark core
*/

$start_time = microtime(true); // start render time

// Includes
include_once 'lowmark/config.php'; // Default configuration
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
    if (($Extra instanceof ParsedownExtra)&&(!$lowmark['raw_html'])) {
        $lowmark['content'] = $Extra->text($resource['content']); // markdown parser
    } else {
        $lowmark['content'] = $resource['content'];
    }

    // Extend Markdown with custom functions
    if ($lowmark['img_to_figure']) $lowmark['content'] = img_to_figure($lowmark['content']); // add <figure> to <img> tags
    if ($lowmark['extend_links'])  $lowmark['content'] = extend_links($lowmark['content']); // extend <a> tags
    if ($lowmark['mail_encode'])   $lowmark['content'] = mail_encode($lowmark['content']); // mail encoding
    if ($lowmark['headline_ids']) ['content' => $lowmark['content'], 'headlines' => $lowmark['headlines'],  ] = headline_ids($lowmark['content'], $lowmark['headline_to_top']); // add unique ids to headlines
    if ($lowmark['details_patch']) $lowmark['content'] = details_patch($lowmark['content']); // <details> workaround

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

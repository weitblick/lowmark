<?php
/*


	██       ██████  ██     ███    ███  █████  ██████  ██   ██
	██      ██    ██ ██     ████  ████ ██   ██ ██   ██ ██  ██
	██      ██    ██ ██  █  ██ ████ ██ ███████ ██████  █████
	██      ██    ██ ██ ███ ██  ██  ██ ██   ██ ██   ██ ██  ██
	███████  ██████   ███ ████      ██ ██   ██ ██   ██ ██   ██

	LOWMARK – A Low-tech Markdown Website Generator
	Version: 0.31 (2025-04-13)
	https://lowmark.de

	by Erhard Maria Klein
	emk@lowmark.de
	CC BY-NC-SA 4.0

	Parsedown & ParsedownExtra from https://parsedown.org/
	highlight.js from https://highlightjs.org/

*/

// ============ LOWMARK MAIN PART ================

$start_time = microtime(true); // start render time

// Includes

include_once 'lowmark/config.php'; // Default Configuration
include_once 'lowmark/frontmatter.php'; // LOWMARK SIMPLE FRONTMATTER PARSER
include_once 'lowmark/components.php'; // lowmark components
include_once 'lowmark/Parsedown.php'; // Markdown Parser. Download from https://github.com/erusev/parsedown
include_once 'lowmark/ParsedownExtra.php'; // Markdown Extra Extention. Download from https://github.com/erusev/parsedown-extra

include_once 'lowmark/custom.php'; // LOEWENSTEIN CODE

// set base url and canonical url
if (empty($lowmark['base_url'])) {
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
} else {
    $base_url = rtrim($lowmark['base_url'], '/');
}
$lowmark['canonical_url'] = $base_url . $_SERVER['REQUEST_URI'];

$path = $_GET['q'] ?? 'index.html'; // Get the path from the GET parameter q, default: index.html

// A little security (no leading / and no ../)
$path = preg_replace('/((|\/)\.\.\/)+/', '/', $path);
$path = ltrim($path, '/');

$converted_path = preg_replace('/\.html$/', '.md', $path); // Change .html into .md
$lowmark['home'] = ($converted_path == 'index.md'); // Identify the homepage
$md_file_path = $lowmark['content_dir'] . $converted_path; // Path to the markdown file in the content directory

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

$end_time = microtime(true); // Determine the execution time of the script
$execution_time = ($end_time - $start_time) * 1000; // Convert to milliseconds
?>

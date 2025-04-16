<?php
// LOWMARK Default Configuration

$lowmark = [

    // Main configuration
    'sitename'            => 'lowmark', // Title of the website
    'description'         => 'A low-tech Markdown website generator', // Default site description
    'base_url'            => 'https://lowmark.de', // Base URL (used for canonical url in siteheader)
    'title'               => '- undefined -', // Default page title (can be set via frontmatter)
    'content_dir'         => 'content/', // Directory for content files

    // Components / features
    'details_patch'       => false, // Enable patch/fix for <details> tag if needed
    'extend_links'        => true,  // Automatically extend links (e.g. with target/rel attributes)
    'headline_ids'        => true,  // Add automatic IDs to headings <h2>–<h6>
    'headline_to_top'     => '',    // Add a “back to top” symbol next to headings, e.g. <a href="#top" title="to top" style="float: right; text-decoration: none;">↑</a>
    'img_to_figure'       => true,  // Convert <img> to <figure> with caption and alignment
    'mail_encode'         => true,  // Obfuscate email addresses automatically
    'raw_html'            => false, // Don’t render raw HTML in Markdown

    // Theme
    'logo'                => '',
];


?>

<?php
/**
 * Lowmark Project Configuration File
 *
 * This file defines settings for your Lowmark-based website.
 * You can customize metadata, menus, theme settings, and other project-specific values here.
 *
 * Place this file in the project root. It will be automatically loaded by index.php via core.php.
 * All settings defined here can be overridden in page frontmatter if needed.
 */

defined('LOWMARK_RUNNING') or (http_response_code(404) && exit('Not found.')); // Prevent direct access to this file

$lowmark = [

    // Main configuration
    'sitename'            => 'lowmark simple theme', // Title of the website
    'description'         => 'the default theme of lowmark – a lowtech markdown website generator', // Default site description
    'title'               => '- undefined -', // Default page title (is normally set via frontmatter)


    // Components / features
    'extend_links'        => true,  // Automatically extend links (e.g. with target/rel attributes)
    'headline_ids'        => true,  // Add automatic IDs to headings <h2>–<h6>
    'headline_to_top'     => '',    // Add a "back to top" symbol next to headings, e.g. <a href="#top" title="to top" style="float: right; text-decoration: none;">↑</a>
    'img_to_figure'       => true,  // Convert <img> to <figure> with caption and alignment
    'mail_encode'         => true,  // Obfuscate email addresses automatically
    'raw_html'            => false, // Don’t render raw HTML in Markdown
    'shortcodes'          => true,  // Enable Shortcodes


// Theme Configuration of "Lowmark Simple Theme"

    // Website logo as inline svg (color via css variable due to darkmode). You can also specify an image tag, e.g. <img src="/path/to/logo.png" alt="Logo" style="width: 150px; height: auto;" />
    'logo' => '<svg style="width: 150px; height: auto;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 810 178" alt="Example logo of lowmark – copyright by https://lowmark.de"><style>.cls-1{fill:var(--logo-color);stroke-width:0px;}</style><path class="cls-1" d="M552.17,55.47c-20.3,0-56.18,10.87-58.29,56.75-2.1,45.88,19.02,59.67,40.75,59.67s44.75-19.22,44.75-19.22v19.22h29.83v-67.2s-5.49-49.21-57.05-49.21ZM535.16,143.29c-10.2,0-10.2-12.87-10.2-12.87v-18.2s1.08-26.14,27.22-26.14c23.68,0,27.22,22.81,27.22,26.14s-30.91,31.06-44.24,31.06ZM37.06,172.04H5V4.96h32.06v167.09ZM120.05,55.58c-32.44,0-58.73,26.3-58.73,58.73s26.3,58.73,58.73,58.73,58.73-26.3,58.73-58.73-26.3-58.73-58.73-58.73ZM120.05,142.83c-15.75,0-28.51-12.77-28.51-28.51s12.77-28.51,28.51-28.51,28.51,12.77,28.51,28.51-12.77,28.51-28.51,28.51ZM470.9,98.53v74.03h-30.76l.13-73.98s-2.55-10.71-14.38-10.69c-11.85.03-14.69,10.69-14.69,10.69l.08,73.98h-31.01l.03-73.9s-3.38-10.79-14.46-10.85c-11.08-.04-14.07,10.77-14.07,10.77v32.14s0,41.84-45.31,41.84c-18.66,0-29.43-11.59-29.43-11.59,0,0-9.22,11.59-30.55,11.59-45.11,0-45.11-41.84-45.11-41.84V63.26h30.76l-.13,67.42s2.53,10.71,14.38,10.68c11.84-.02,14.68-10.68,14.68-10.68l-.08-67.42h31.01l-.02,67.34s3.38,10.79,14.45,10.83c11.08.06,14.07-10.76,14.07-10.76v-32.15s0-41.83,45.32-41.83c18.66,0,29.42,11.58,29.42,11.58,0,0,9.24-11.58,30.56-11.58,45.11,0,45.11,41.83,45.11,41.83ZM693.04,86.08s-14.56,0-18.25,0c-7.92,0-10.05,7.2-10.05,9.89v75.92h-30.91v-78.12c0-11.23,9.38-36.91,33.68-36.91h25.53v29.22ZM744.56,98.84c17.17,0,29.22-14.16,29.22-23.84v-18.15h31.22v18.15c0,23.84-18.95,39.37-18.95,39.37,0,0,18.95,15.69,18.95,39.68s0,17.84,0,17.84h-31.22v-17.84c0-15.5-18.59-23.99-29.22-23.99v41.83h-31.83V56.86h31.83v41.98Z"/></svg>',

    // Menu
    'menu' => [
                'index.html'   => 'Home',
                'example.html' => 'Example Page',
                // 'sub/'         => 'Submenu',
                // 'sub/sub.html' => 'Subpage',
               ],

];

?>

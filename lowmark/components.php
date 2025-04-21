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
 * File:         components.php
 * Version:      0.4
 * Last updated: 2025-04-16
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Additional features for images, links, mailencoding and headings
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 */

// expand <img> tags to <figure><img><figcaption></figcaption></figure> and add lazy loading and alignment
function img_to_figure($html) {
    $html = preg_replace('/<p>(<img\s+[^>]+>)<\/p>/', '$1', $html); // Remove enclosing <p> tags if necessary

    // Replacing the <img> tag with <figure> tags
    $pattern = '/<img\s+([^>]*)>/';
    $html = preg_replace_callback($pattern, function($matches) {
        $img_tag = $matches[0];
        $attributes = $matches[1];
        $align = '';

        // Check for alignment in alt attribute
        if (preg_match('/alt=":((left|right|center)(\s*))/', $attributes, $alt_matches)) {
            $attributes = str_replace(':' . $alt_matches[1], '', $attributes); // Remove the alignment part from the alt attribute
            $align = trim($alt_matches[1]); // align without trailing spaces
            $img_tag = '<img ' . $attributes . '>'; // Rebuild the img tag with modified attributes
        }

        // Add loading="lazy"
        $img_tag = preg_replace('/\s*\/>$/', ' loading="lazy" />', $img_tag);

        // Build the figure tag
        $figure_tag = '<figure';
        if ($align) {
            $figure_tag .= ' class="lowmark-' . $align . '"';
        }
        $figure_tag .= ">$img_tag";

        // Use the title attribute for <figcaption> - if available
        if (preg_match('/title="([^"]*)"/', $attributes, $title_matches)) {
            $caption = $title_matches[1];
            $caption = html_entity_decode(html_entity_decode($caption, ENT_QUOTES), ENT_QUOTES); // prevent double encoding of special characters
            $figure_tag .= "<figcaption>$caption</figcaption>";
        }
        $figure_tag .= "</figure>";

        return $figure_tag;
    }, $html);

    return $html;
}

// Replace internal links to *.md with *.html and extend external links with target="_blank"
function extend_links($content) {
    $pattern = '/<a\s+(.*?)href=["\'](.*?\.md)["\'](.*?)>(.*?)<\/a>/i'; // Regular expression to identify internal .md links
    $content = preg_replace_callback($pattern, function($matches) {
        $tag = $matches[0];
        $attributes = $matches[1];
        $href = $matches[2];
        $rest = $matches[3];
        $link_text = $matches[4];
        
        if (strpos($href, '://') === false) { // Check that the path is local
            $new_href = str_replace('.md', '.html', $href); // Replace *.md with *.html
            $new_tag = "<a $attributes href=\"$new_href\"$rest>$link_text</a>"; // Create the new <a> tag
            return $new_tag;
        } else {
            return $tag; // do nothing if link is external
        }
    }, $content);
    
    // Regular expression to extend the <a> tags with target="_blank" if they are external
    $pattern = '/<a\s+([^>]*)href=(["\'])(.*?)(?:\\2)([^>]*)>/i';

    $content = preg_replace_callback($pattern, function($matches) {
        $attributes = $matches[1];
        $href = $matches[3];
        $rest = $matches[4];
        if (strpos($href, '://') !== false) { // Check whether the link is an external URL (contains ://)
            return "<a$attributes href=\"$href\" target=\"_blank\"$rest>"; // Add target="_blank"
        } else {
            return $matches[0]; // No changes required
        }
    }, $content);
    
    return $content;
}

// Find all email addresses in the content and call email encoding function
function mail_encode($content) {
    preg_match_all('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', $content, $matches);

    // Iterate through each found email address and replace them with the cloaked version
    foreach ($matches[0] as $address) {
        $content = str_replace($address, generate_cloaked_email_link($address), $content);
    }

    return $content;
}

// Email encoding
function generate_cloaked_email_link($address) {

    // Get user and domain parts of the email address
    $parts = explode("@", $address);
    $user = $parts[0];
    $domain = isset($parts[1]) ? $parts[1] : "";

    $fingerprint = md5($address . "mailto" . str_shuffle(implode(range(0, 999)))[0]); // Compute md5 fingerprint

    // Generate cloaked email span
    $user_reversed = strrev($user);
    $domain_reversed = strrev($domain);
    $user_chars = str_split($user_reversed);
    $domain_chars = str_split($domain_reversed);
    $span_attributes = 'data-user="' . implode('', $user_chars) . '"';
    if (!empty($domain)) {
        $span_attributes .= ' data-domain="' . implode('', $domain_chars) . '"';
    }
    $span_element = '<span class="cloaked-e-mail" ' . $span_attributes . '></span>';

    // Generate JavaScript code
    $script_code = <<<EOD
<script id="$fingerprint">
      var scriptTag = document.getElementById("$fingerprint");
      var wblink = document.createElement("a");
      var address = "$user_reversed".split('').reverse().join('') + "@" + "$domain_reversed".split('').reverse().join('');
      wblink.href = "mailto:" + address;
      wblink.innerText = address;
      scriptTag.parentElement.insertBefore(wblink, scriptTag.previousElementSibling);
      scriptTag.parentElement.removeChild(scriptTag.previousElementSibling);
</script>
EOD;

    return $span_element . $script_code;
}

// add unique ids to headlines
function headline_ids($content, $to_top) {
    // Regex pattern to match headline tags
    $pattern = '/<h([1-6])>(.*?)<\/h\1>/i';
    $headlines = [];

    // Function to generate a valid ID from a headline
    $generate_id = function($headline) {
        // Convert to lowercase
        $headline = strtolower($headline);
        // Transliterate German umlauts and ß
        $translit = [
            'ä' => 'ae', 'ö' => 'oe', 'ü' => 'ue', 'ß' => 'ss'
        ];
        $headline = strtr($headline, $translit);
        // Remove special characters and replace spaces with hyphens
        $id = preg_replace('/[^a-z0-9]+/i', '-', trim($headline));
        // Ensure the ID is unique by appending a suffix if necessary
        static $ids = [];
        $original_id = $id;
        $count = 1;
        while (isset($ids[$id])) {
            $id = $original_id . '-' . $count++;
        }
        $ids[$id] = true;
        return $id;
    };

    // Callback function to replace headline tags with IDs
    $callback = function($matches) use (&$headlines, $generate_id, $to_top) {
        $level = $matches[1];
        $headline = $matches[2];
        $id = $generate_id($headline);
        $headlines[$id] = $headline;
        $content = '';
        $content .= "<h{$level} id=\"{$id}\">{$headline}{$to_top}</h{$level}>";
        return $content;
    };

    // Replace headline tags with IDs
    $content = preg_replace_callback($pattern, $callback, $content);

    return ['content' => $content, 'headlines' => $headlines];
}

// Load Shortcodes
function load_shortcodes($dir = 'shortcodes') {
    if (!is_dir($dir)) {
        return;
    }

    foreach (glob("$dir/*.php") as $file) {
        include_once $file;
    }
}

load_shortcodes();

// Add <details> as a workaround with HTML comments
// deprecated
function details_patch($content) {
    $content = preg_replace('/<!-- DETAILS (.*) -->/', "<details><summary>$1</summary>", $content); // replace the DETAILS HTML comment (start)
    $content = str_replace("<!-- /DETAILS -->", "</details>", $content); // replace the DETAILS HTML comment (end)
    return $content;
}

?>

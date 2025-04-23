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
 * Version:      0.5
 * Last updated: 2025-04-23
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Additional features for images, links, mailencoding and headings
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 */

// expand <img> tags to <figure><img><figcaption></figcaption></figure> and add lazy loading, alignment and image resizing
function img_to_figure($lowmark) {
    // Remove enclosing <p> tags around <img> tags
    $content = preg_replace('/<p>(<img\s+[^>]+>)<\/p>/', '$1', $lowmark['content']);

    $default_resize = $lowmark['image_resize'] ?? '';
    $image_format = $lowmark['image_format'] ?? '';
    $image_quality = $lowmark['image_quality'] ?? '';

    // Replace <img> tags with <figure> structure
    $pattern = '/<img\s+([^>]*?)(?:\s*\/)?>/';
    $content = preg_replace_callback($pattern, function($matches) use ($default_resize, $image_format, $image_quality) {
        $img_tag = $matches[0];
        $attributes = $matches[1];
        $align = '';
        $image_resize = $default_resize;

        // Extract src attribute
        $src = '';
        if (preg_match('/src="([^"]+)"/', $attributes, $src_matches)) {
            $src = $src_matches[1];
        }

        // Extract and parse alt attribute
        if (preg_match('/alt="([^"]*)"/', $attributes, $alt_matches)) {
            $alt = $alt_matches[1];

            // Check for alignment keyword (:left, :right, :center)
            if (preg_match('/:(left|right|center)\b/', $alt, $align_match)) {
                $align = $align_match[1];
                $alt = str_replace(':' . $align, '', $alt);
            }

            // Check for resize instruction (:800x600, :750x, :x400)
            if (preg_match('/:([0-9]*x[0-9]*)\b/', $alt, $size_match)) {
                $image_resize = $size_match[1];
                $alt = str_replace(':' . $size_match[1], '', $alt);
            }

            // Clean up the alt text
            $alt = trim(preg_replace('/\s+/', ' ', $alt));

            // Replace alt attribute in the tag
            $attributes = preg_replace('/alt="[^"]*"/', 'alt="' . htmlspecialchars($alt, ENT_QUOTES) . '"', $attributes);
        }

        // Modify src if image resizing is enabled
        if ($image_resize && $src) {
            $scaled = scale_image($src, $image_resize, $image_format, $image_quality);
            $new_src = $scaled['src'];
            $attributes = preg_replace('/src="[^"]+"/', 'src="' . htmlspecialchars($new_src, ENT_QUOTES) . '"', $attributes);

            // Set width/height attributes if available
            if (!empty($scaled['width'])) {
                $attributes .= ' width="' . (int)$scaled['width'] . '"';
            }
            if (!empty($scaled['height'])) {
                $attributes .= ' height="' . (int)$scaled['height'] . '"';
            }
        }

        $img_tag = '<img ' . $attributes . ' loading="lazy" />'; // Rebuild the <img> tag with lazy loading

        // Start building the <figure> tag
        $figure_tag = '<figure';
        if ($align) {
            $figure_tag .= ' class="lowmark-' . $align . '"';
        }
        $figure_tag .= ">$img_tag";

        // Add <figcaption> if a title attribute is present
        if (preg_match('/title="([^"]*)"/', $attributes, $title_matches)) {
            $caption = html_entity_decode(html_entity_decode($title_matches[1], ENT_QUOTES), ENT_QUOTES);
            $figure_tag .= "<figcaption>$caption</figcaption>";
        }

        $figure_tag .= "</figure>";

        return $figure_tag;
    }, $content);

    return $content;
}

function scale_image($src, $image_resize, $image_format, $image_quality) {
    $cache_dir = 'image-cache/';
    $content_prefix = 'content/';
    $source_path = $content_prefix . ltrim($src, '/');

    if (!file_exists($source_path)) {
        return ['src' => $src];
    }

    // Parse resize string
    $width = null;
    $height = null;
    if (preg_match('/^([0-9]*)x([0-9]*)$/', $image_resize, $matches)) {
        $width = $matches[1] !== '' ? (int)$matches[1] : null;
        $height = $matches[2] !== '' ? (int)$matches[2] : null;
    } else {
        return ['src' => $src];
    }

    // Fallback bei fehlenden Libs
    if (!function_exists('imagecreatetruecolor') && !class_exists('Imagick')) {
        return ['src' => $src];
    }

    $relative_path = preg_replace('#^content/#', '', ltrim($src, '/'));
    $extension = pathinfo($relative_path, PATHINFO_EXTENSION);
    $base_name = preg_replace('/\.' . preg_quote($extension, '/') . '$/', '', $relative_path);

    $format = strtolower($image_format ?: $extension);
    $size_label = ($width ?: '') . 'x' . ($height ?: '');
    $quality_label = 'q' . (int)$image_quality;
    $cached_filename = $base_name . '_' . $size_label . $quality_label . '.' . $format;
    $cached_path = $cache_dir . $cached_filename;

    if (file_exists($cached_path)) {
        return ['src' => $cached_path, 'width' => $width, 'height' => $height];
    }

    $cache_subdir = dirname($cached_path);
    if (!is_dir($cache_subdir)) {
        mkdir($cache_subdir, 0755, true);
    }

    // Try Imagick
    if (extension_loaded('imagick')) {
        try {
            $image = new Imagick($source_path);
            $orig_width = $image->getImageWidth();
            $orig_height = $image->getImageHeight();

            if ($width && $height) {
                $src_ratio = $orig_width / $orig_height;
                $dst_ratio = $width / $height;

                if ($src_ratio > $dst_ratio) {
                    $new_width = (int)($orig_height * $dst_ratio);
                    $x = (int)(($orig_width - $new_width) / 2);
                    $image->cropImage($new_width, $orig_height, $x, 0);
                } else {
                    $new_height = (int)($orig_width / $dst_ratio);
                    $y = (int)(($orig_height - $new_height) / 2);
                    $image->cropImage($orig_width, $new_height, 0, $y);
                }

                $image->resizeImage($width, $height, Imagick::FILTER_LANCZOS, 1);
            } elseif ($width || $height) {
                $image->resizeImage($width ?: 0, $height ?: 0, Imagick::FILTER_LANCZOS, 1);
                $width = $image->getImageWidth();
                $height = $image->getImageHeight();
            }

            $image->setImageFormat($format);
            $image->setImageCompressionQuality((int)$image_quality);
            $image->writeImage($cached_path);
            $image->clear();
            $image->destroy();

            return ['src' => $cached_path, 'width' => $width, 'height' => $height];
        } catch (Exception $e) {}
    }

    // Try GD
    if (extension_loaded('gd')) {
        $info = getimagesize($source_path);
        if (!$info) return ['src' => $src];

        list($orig_width, $orig_height) = $info;
        $mime = $info['mime'];

        switch ($mime) {
            case 'image/jpeg': $source_img = imagecreatefromjpeg($source_path); break;
            case 'image/png':  $source_img = imagecreatefrompng($source_path); break;
            case 'image/gif':  $source_img = imagecreatefromgif($source_path); break;
            case 'image/webp': $source_img = imagecreatefromwebp($source_path); break;
            default: return ['src' => $src];
        }

        if ($width && $height) {
            $src_ratio = $orig_width / $orig_height;
            $dst_ratio = $width / $height;

            if ($src_ratio > $dst_ratio) {
                $crop_width = (int)($orig_height * $dst_ratio);
                $crop_height = $orig_height;
                $src_x = (int)(($orig_width - $crop_width) / 2);
                $src_y = 0;
            } else {
                $crop_width = $orig_width;
                $crop_height = (int)($orig_width / $dst_ratio);
                $src_x = 0;
                $src_y = (int)(($orig_height - $crop_height) / 2);
            }

            $resized_img = imagecreatetruecolor($width, $height);
            imagecopyresampled($resized_img, $source_img, 0, 0, $src_x, $src_y, $width, $height, $crop_width, $crop_height);
        } elseif ($width || $height) {
            if ($width && !$height) {
                $height = intval($orig_height * $width / $orig_width);
            } elseif (!$width && $height) {
                $width = intval($orig_width * $height / $orig_height);
            }

            $resized_img = imagecreatetruecolor($width, $height);
            imagecopyresampled($resized_img, $source_img, 0, 0, 0, 0, $width, $height, $orig_width, $orig_height);
        } else {
            return ['src' => $src];
        }

        switch ($format) {
            case 'jpeg':
            case 'jpg':
                imagejpeg($resized_img, $cached_path, (int)$image_quality);
                break;
            case 'png':
                imagepng($resized_img, $cached_path, min(9, max(0, 9 - round($image_quality / 10))));
                break;
            case 'webp':
                imagewebp($resized_img, $cached_path, (int)$image_quality);
                break;
            default:
                return ['src' => $src];
        }

        imagedestroy($resized_img);
        imagedestroy($source_img);

        return ['src' => $cached_path, 'width' => $width, 'height' => $height];
    }

    return ['src' => $src];
}

// Replace internal links to *.md with *.html and extend external links with target="_blank"
function extend_links($lowmark) {
    $content = $lowmark['content'];
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
function mail_encode($lowmark) {
    $content = $lowmark['content'];
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
function headline_ids($lowmark) {
    $content = $lowmark['content'];
    $to_top  = $lowmark['headline_to_top'];

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

// Include shortcodes
function load_shortcodes($dir = 'shortcodes') {
    if (!is_dir($dir)) {
        return;
    }

    foreach (glob("$dir/*.php") as $file) {
        include_once $file;
    }
}

// Inline shortcodes
function render_shortcodes($lowmark) {
    $content = $lowmark['content'];
    // Regex to match opening or closing shortcode comments
    // e.g. <!-- [details "Summary"] --> ... <!-- [/details] -->
    $pattern = '/<!--\s*\[\/?([a-z0-9_]+)(.*?)\]\s*-->/is';

    // Array to hold final output parts
    $output = [];
    $offset = 0;

    // Find all shortcode tags
    if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $full_tag = $match[0][0];
            $tag_pos = $match[0][1];
            $keyword = strtolower($match[1][0]);
            $raw_attrs = trim($match[2][0]);

            // Append content before this tag
            $output[] = substr($content, $offset, $tag_pos - $offset);
            $offset = $tag_pos + strlen($full_tag);

            // Build attributes array
            $attrs = parse_shortcode_attributes($raw_attrs);

            // Check if it's a closing tag
            if (strpos($match[0][0], '[/' . $keyword) !== false) {
                $attrs['end'] = true; // or use 'closing' if you prefer
            }

            // Include shortcode handler
            $handler_file = "shortcodes/{$keyword}.php";
            if (file_exists($handler_file)) {
                include_once($handler_file);
                $func = $keyword . '_shortcode';

                if (function_exists($func)) {
                    // Call the shortcode function
                    $output[] = $func($attrs);
                } else {
                    // Function not defined
                    $output[] = "<!-- shortcode function '$func' not found -->";
                }
            } else {
                // Handler file not found
                $output[] = "<!-- shortcode handler for '$keyword' not found -->";
            }
        }

        // Append remaining content after last match
        $output[] = substr($content, $offset);
        return implode('', $output);
    }

    return $content; // no matches found
}

function parse_shortcode_attributes($text) {
    $attrs = [];
    $index = 1;

    // Matches key="value" or "value" or bareword
    $pattern = '/(\w+)\s*=\s*"([^"]*)"|"(.*?)"|(\S+)/';

    if (preg_match_all($pattern, $text, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $m) {
            if (!empty($m[1])) {
                // key="value"
                $attrs[strtolower($m[1])] = $m[2];
            } elseif (!empty($m[3])) {
                // "value" (positional)
                $attrs['attribute_' . $index++] = $m[3];
            } elseif (!empty($m[4])) {
                // bareword (positional)
                $attrs['attribute_' . $index++] = $m[4];
            }
        }
    }

    return $attrs;
}

?>

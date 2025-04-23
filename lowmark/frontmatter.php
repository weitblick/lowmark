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
 * File:         frontmatter.php
 * Version:      0.5
 * Last updated: 2025-04-23
 * Homepage:     https://lowmark.de
 * Repository:   https://github.com/weitblick/lowmark
 *
 * Description:  Simple frontmatter parser
 *
 * Copyright (c) 2025 Erhard Maria Klein, lowmark.de
 * Licensed under the MIT License
 * See LICENSE file or https://opensource.org/licenses/MIT
 */

function parse_frontmatter($markdown) {
    $frontmatter = []; // Will hold the parsed key-value pairs from the frontmatter
    $content = '';     // Will hold the actual markdown content (without frontmatter)

    // Split the markdown input into individual lines, supporting all line endings
    $lines = preg_split('/\R/', $markdown);
    $line_count = count($lines);

    // Check if the first line is the opening frontmatter delimiter '---'
    if ($line_count > 0 && trim($lines[0]) === '---') {

        $end_index = null; // To store the index of the closing frontmatter delimiter

        // Look for the next '---' that marks the end of frontmatter
        for ($i = 1; $i < $line_count; $i++) {
            if (trim($lines[$i]) === '---') {
                $end_index = $i;
                break;
            }
        }

        // If closing delimiter was found, extract and parse frontmatter
        if ($end_index !== null) {
            // Extract lines that are part of the frontmatter block
            $front_lines = array_slice($lines, 1, $end_index - 1);

            // Remaining lines are considered the actual markdown content
            $body_lines = array_slice($lines, $end_index + 1);

            // Convert frontmatter lines into a YAML string
            $front_yaml = implode("\n", $front_lines);

            // Parse the YAML string into a PHP associative array
            $frontmatter = parse_simple_yaml($front_yaml);

            // Combine the content lines back into a single string
            $content = implode("\n", $body_lines);
        } else {
            // If no closing '---' is found, treat everything as content
            $content = $markdown;
        }
    } else {
        // If the first line is not '---', treat the whole input as content
        $content = $markdown;
    }

    // Return both the frontmatter and the markdown content
    return ['frontmatter' => $frontmatter, 'content' => $content];
}


function parse_simple_yaml($yaml_string) {
    // Split the YAML string into individual lines
    $lines = explode("\n", $yaml_string);
    $result = [];         // Resulting associative array
    $current_key = null;  // Keeps track of the last key to support multi-line lists

    foreach ($lines as $line) {
        $trimmed = trim($line);

        // Skip empty lines and lines that start with a comment character "#"
        if ($trimmed === '' || str_starts_with($trimmed, '#')) {
            continue;
        }

        // Simple list item (block list) – added under the last parsed key
        // ⚠️ Limitations:
        //   - No escape support within quoted items
        //   - No inline comments
        //   - No multi-line values
        // For advanced lists, prefer using inline JSON-style arrays below.
        if (preg_match('/^\s*-\s*(.+)$/', $line, $matches) && $current_key !== null) {
            $item = trim($matches[1], " \t\n\r\0\x0B\"'"); // Strip quotes and whitespace
            $result[$current_key][] = $item; // Append item to the current list
            continue;
        }

        // Handle key-value pair (e.g., title: "My Page")
        if (str_contains($line, ':')) {
            // Split the line into key and value (only at the first colon)
            list($key, $val) = explode(':', $line, 2) + [null, ''];
            $key = trim($key);
            $val = trim($val);

            // JSON-compatible inline list (e.g., ["a", "b, c"])
            // This hack allows for quoted values with commas, escapes, etc.
            if (preg_match('/^\[.*\]$/', $val)) {
                $decoded = json_decode($val, true);
                if (is_array($decoded)) {
                    $val = $decoded;
                }
            }

            // Handle escaped characters (e.g., \" becomes ")
            $val = stripslashes($val);

            // Convert boolean strings to actual booleans
            if ($val === 'true') {
                $val = true;
            } elseif ($val === 'false') {
                $val = false;
            }

            // For all other values: trim surrounding quotes and whitespace
            else {
                $val = trim($val, "\"' \t\n\r\0\x0B");
            }

            // Assign the key-value pair
            $result[$key] = $val;

            // If the value is an array, remember the key to allow appending list items,
            // which allows you to add block list elements to single line lists, e.g:
            // tags: ["one", "two"]
            // - three
            $current_key = is_array($val) ? $key : null;
        }
    }

    // Return the parsed key-value structure
    return $result;
}

?>
